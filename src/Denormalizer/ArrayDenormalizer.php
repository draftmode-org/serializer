<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Terrazza\Component\Serializer\Annotation\IAnnotationFactory;
use Terrazza\Component\Serializer\Annotation\IAnnotationType;
use Terrazza\Component\Serializer\IDenormalizer;
use Terrazza\Component\Serializer\TraceKeyTrait;

class ArrayDenormalizer implements IDenormalizer {
    use TraceKeyTrait;
    private LoggerInterface $logger;
    private IAnnotationFactory $annotationFactory;
    private bool $restrictUnInitialized=false;
    private bool $restrictArguments=false;

    public function __construct(LoggerInterface $logger, IAnnotationFactory $annotationFactory) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = $annotationFactory;
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param class-string<T>|T $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) : object {
        $logMethod                                  = __METHOD__."()";
        if ($restrictUnInitialized)
            $this->restrictUnInitialized            = true;
        if ($restrictArguments)
            $this->restrictArguments                = true;
        $this->logger->debug($logMethod." ".(is_object($className) ?
            "object: " .basename(get_class($className)) :
            "class: " . $className),
            ["line" => __LINE__]);
        $unmappedKeys                               = [];
        if (is_string($className)) {
            $object                                 = $this->createObject($className, $input);
            if (is_array($input) && count($input)) {
                $unmappedKeys                       = $this->updateObject($object, $input, false);
            }
        } elseif (is_object($className)) {
            /** @var T $object */
            $object                                 = $this->cloneClass($className);
            if (is_array($input) && count($input)) {
                $unmappedKeys                       = $this->updateObject($object, $input, true);
            }
        } else {
            throw new RuntimeException("className $className is either an object nor a valid className");
        }
        if ($this->restrictUnInitialized) {
            $this->isInitializedObject($object);
        }
        if ($this->restrictArguments && is_array($unmappedKeys) && count($unmappedKeys)) {
            $traceKeys                              = $this->getTraceKeys();
            $traceKeys                              .= (strlen($traceKeys)) ? "." : "";
            throw new InvalidArgumentException($traceKeys."(".join(", ", $unmappedKeys).") are passed but unknown/not allowed");
        }
        return $object;
    }

    /**
     * @param class-string<T> $className
     * @return T
     * @template T of object
     * @param mixed $input
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function createObject(string $className, &$input) : object {
        $logMethod                                  = __METHOD__."()";
        $this->logger->debug("$logMethod createObject $className",
            ["line" => __LINE__, "arguments" => $input]);
        if (class_exists($className)) {
            $reflect 						        = new ReflectionClass($className);
            $constructor                            = $reflect->getConstructor();
            if ($constructor && $constructor->isPublic()) {
                $methodValues						= $this->getMethodValues($constructor, $input);
                if (is_array($methodValues)) {
                    $object                         = $reflect->newInstance(...$methodValues);
                } else {
                    $object                         = $reflect->newInstance($methodValues);
                }
            } else {
                $object                             = $reflect->newInstanceWithoutConstructor();
            }
            return $object;
        } else {
            $this->logger->error($logMethod." ".$message = "class $className does not exists");
            throw new RuntimeException($message);
        }
    }

    /**
     * @param T $className
     * @return T
     * @template T of object
     */
    private function cloneClass(object $className) : object {
        return unserialize(serialize($className));
    }

    /**
     * @param object $object
     * @param array $input
     * @param bool $updateObject
     * @return array|null
     * @throws ReflectionException
     */
    private function updateObject(object $object, array &$input, bool $updateObject) :?array {
        $logMethod                                  = __METHOD__."()";
        $this->logger->debug("$logMethod updateObject ".get_class($object),
            ["line" => __LINE__, "arguments" => $input]);
        $reflect                                    = new ReflectionClass($object);
        $unmappedKeys                               = [];
        foreach ($input as $inputKey => $inputValue) {
            if (is_string($inputKey)) {
                $this->logger->debug("$logMethod updateObject inputKey: $inputKey",
                    ["line" => __LINE__, "inputValue" => $inputValue]);
                $setMethodName                      = "set" . ucfirst($inputKey);
                if ($reflect->hasMethod($setMethodName)) {
                    $this->logger->debug("$logMethod updateObject use method: $setMethodName",
                        ["line" => __LINE__]);
                    //
                    // getMethod for inputKey exists
                    // && returnType is a class
                    // => get current object and denormalize it
                    //
                    $getObject                      = null;
                    if ($updateObject && is_array($inputValue)) {
                        $getMethodName              = "get" . ucfirst($inputKey);
                        if ($reflect->hasMethod($getMethodName)) {
                            $getMethod              = $reflect->getMethod($getMethodName);
                            $getMethodReturnType    = $this->annotationFactory->getAnnotationReturnType($getMethod);
                            $this->logger->debug("$logMethod $getMethodName returnType", [
                                "line"              => __LINE__,
                                "isBuiltIn"         => $getMethodReturnType->isBuiltIn(),
                                "isArray"           => $getMethodReturnType->isArray(),
                                "isOptional"        => $getMethodReturnType->isOptional(),
                                "type"              => $getMethodReturnType->getType(),
                            ]);
                            if (!$getMethodReturnType->isBuiltIn() &&
                                !$getMethodReturnType->isArray() &&
                                $getMethodReturnType->getType() !== null) {
                                $this->logger->debug("$logMethod fetch object via $getMethodName");
                                $getObject          = $getMethod->invoke($object);
                            }
                        }
                    }
                    $setMethod                      = $reflect->getMethod($setMethodName);
                    if ($getObject) {
                        $methodValues               = $this->denormalize($getObject, $inputValue);
                    } else {
                        $parameters                 = $setMethod->getParameters();
                        //
                        // getMethod has only one parameter
                        // => forward inputValues wrapped to first parameter key to get method
                        //
                        if (count($parameters) === 1) {
                            $parameter              = array_shift($parameters);
                            //
                            // in case of inputValue === null
                            // ==> protect isOptional
                            //
                            if (is_null($inputValue)) {
                                if ($parameter->isOptional()) {
                                    $setValues      = null;
                                } else {
                                    throw new InvalidArgumentException($this->getTraceKeys()." is required, given null");
                                }
                            } else {
                                $parameterKey       = $parameter->getName();
                                $setValues          = [$parameterKey => $inputValue];
                            }
                        //
                        // => forward inputValues 1:1 to get method
                        //
                        } else {
                            $setValues              = $inputValue;
                        }
                        if (is_null($setValues)) {
                            $methodValues           = $setValues;
                        } else {
                            $methodValues           = $this->getMethodValues($setMethod, $setValues);
                        }
                    }
                    if (is_null($methodValues)) {
                        $setMethod->invoke($object, null);
                    }
                    elseif (is_array($methodValues)) {
                        $this->logger->debug("$logMethod ...set", $methodValues);
                        $setMethod->invoke($object, ...$methodValues);
                    } else {
                        $setMethod->invoke($object, $methodValues);
                    }
                } else {
                    $this->logger->debug("$logMethod updateObject method $setMethodName not found",
                        ["line" => __LINE__]);
                    $unmappedKeys[]                 = $inputKey;
                }
            }
        }
        return count($unmappedKeys) ? $unmappedKeys : null;
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $input
     * @return array
     */
    private function mapMethodValues(ReflectionMethod $method, &$input) : array {
        $methodValues                               = [];
        if ($method->isPublic()) {
            $parameters                             = $method->getParameters();
            if (is_array($input)) {
                if (isset($input[0])) {
                    $parameter                      = array_shift($parameters);
                    $valueKey                       = $parameter->getName();
                    $methodValues[$valueKey]        = $input;
                } else {
                    foreach ($parameters as $parameter) {
                        $valueKey                   = $parameter->getName();
                        if (array_key_exists($valueKey, $input)) {
                            $methodValues[$valueKey]= $input[$valueKey];
                            //
                            // protect further usage of same key
                            //
                            unset($input[$valueKey]);
                        }
                    }
                }
            } else {
                $parameter                          = array_shift($parameters);
                $valueKey                           = $parameter->getName();
                $methodValues[$valueKey]            = $input;
            }
        }
        return $methodValues;
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $input
     * @return mixed
     * @throws LogicException
     * @throws ReflectionException
     */
    private function getMethodValues(ReflectionMethod $method, &$input) {
        $logMethod                                  = __METHOD__."()";
        $methodValues								= [];
        $this->logger->debug("$logMethod original input", ["arguments" => $input]);
        $inputValues                                = $this->mapMethodValues($method, $input);
        $this->logger->debug("$logMethod mapped input", ["arguments" => $inputValues]);
        $parameters 							    = $method->getParameters();
        foreach ($parameters as $parameter) {
            $aParameter                             = $this->annotationFactory->getAnnotationParameter($method, $parameter);
            $this->logger->debug("$logMethod input, aParameter",
                [
                    "line"                          => __LINE__,
                    "name"                          => $aParameter->getName(),
                    "isArray"                       => $aParameter->isArray(),
                    "isVariadic"                    => $aParameter->isVariadic(),
                    "isBuiltIn"                     => $aParameter->isBuiltIn(),
                    "type"                          => $aParameter->getType(),
                    "declaringClass"                => $aParameter->getDeclaringClass(),
                ]
            );
            $pKey                                   = $aParameter->getName();
            $this->pushTraceKey($pKey);
            if (array_key_exists($pKey, $inputValues)) {
                $inputValue                         = $inputValues[$pKey];
            } else {
                $inputValue                         = null;
            }
            if (is_null($inputValue)) {
                if ($aParameter->isDefaultValueAvailable()) {
                    $methodValue                    = $aParameter->getDefaultValue();
                    $this->logger->debug($logMethod." ".$this->getTraceKeys()." use defaultValue",
                        ["line" => __LINE__, 'methodValue' => $methodValue]);
                } elseif ($aParameter->isOptional()) {
                    $methodValue                    = null;
                    $this->logger->debug($logMethod." ".$this->getTraceKeys(). " isOptional",
                        ["line" => __LINE__, 'methodValue' => $methodValue]);
                } else {
                    throw new InvalidArgumentException($this->getTraceKeys() . " is required");
                }
            } else {
                //$this->getBuiltInInputValue();
                if ($aParameter->isBuiltIn()) {
                    $methodValue                    = $this->getBuiltInInputValue($aParameter, $inputValue);
                } else {
                    if ($typeClassName = $aParameter->getType()) {
                        /** @var class-string $typeClassName */
                        if ($aParameter->isArray()) {
                            if (!is_array($inputValue)) {
                                throw new InvalidArgumentException($this->getTraceKeys()." expected type array, given ".gettype($inputValue));
                            }
                            $inValues               = [];
                            foreach ($inputValue as $inValue) {
                                $inValues[]         = $this->denormalize($typeClassName, $inValue);
                            }
                            $methodValue            = $inValues;
                        } else {
                            $methodValue            = $this->denormalize($typeClassName, $inputValue);
                        }
                    }
                    else {
                        //
                        // use input for parameter (should be the first one)
                        // => unset input to prevent multiple usage
                        //
                        $methodValue                = $inputValue;
                        $input                      = null;
                    }
                }
            }
            if ($aParameter->isVariadic()) {
                if (is_array($methodValue)) {
                    $methodValues                   += $methodValue;
                }
            } else {
                $methodValues[]                     = $methodValue;
            }
            $this->popTraceKey();
        }
        return $methodValues;
    }


    /**
     * @param mixed $input
     * @return string
     */
    private function getInputType($input) : string {
        $inputType 									= gettype($input);
        switch ($inputType) {
            case "integer":
                return "int";
            case "double":
                return "float";
        }
        return $inputType;
    }

    /**
     * @param IAnnotationType $parameter
     * @param mixed $input
     * @return mixed
     */
    private function getBuiltInInputValue(IAnnotationType $parameter, $input) {
        $inputType 									= $this->getInputType($input);
        $parameterType 								= $parameter->getType();
        if ($parameterType !== $inputType) {
            if ($parameterType === "string" && $inputType === "int") {
                return (string)$input;
            }
            if ($parameterType === "string" && $inputType === "float") {
                return (string)$input;
            }
            if ($parameterType === "float" && $inputType === "int") {
                return floatval($input);
            }
            if ($inputType === "array" && $parameter->isArray()) {
                return $input;
            }
            $traceKey                               = $this->getTraceKeys();
            $this->popTraceKey();
            throw new InvalidArgumentException("$traceKey expected type $parameterType, given $inputType");
        }
        return $input;
    }

    /**
     * @param object $object
     * @throws RuntimeException
     */
    private function isInitializedObject(object $object) : void {
        $reflect                                    = new ReflectionClass($object);
        foreach ($reflect->getProperties() as $property) {
            $property->setAccessible(true);
            if (!$property->isInitialized($object)) {
                throw new RuntimeException("property ".basename(get_class($object))."::".$property->getName()." has not been initialized");
            }
        }
    }
}