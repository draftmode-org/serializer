<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationParameter;
use Terrazza\Component\Serializer\Annotation\AnnotationTypeInterface;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\TraceKeyTrait;

class ArrayDenormalizer implements DenormalizerInterface {
    use TraceKeyTrait;
    private LogInterface $logger;
    private AnnotationFactoryInterface $annotationFactory;

    public function __construct(LogInterface $logger, AnnotationFactoryInterface $annotationFactory) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = $annotationFactory;
    }

    /**
     * @param string|class-string<T>|object $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    private static bool $restrictUnInitialized=false;
    private static bool $restrictArguments=false;
    public function denormalize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) : object {
        if ($restrictUnInitialized)
            self::$restrictUnInitialized            = true;
        if ($restrictArguments)
            self::$restrictArguments                = true;
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug((is_object($className) ?
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
            $object                                 = $className;
            if (is_array($input) && count($input)) {
                $unmappedKeys                       = $this->updateObject($object, $input, true);
            }
        } else {
            throw new RuntimeException("className $className is either an object nor a valid className");
        }
        if (self::$restrictUnInitialized) {
            $this->isInitializedObject($object);
        }
        if (self::$restrictArguments && count($unmappedKeys)) {
            $traceKeys                              = $this->getTraceKeys();
            $traceKeys                              .= (strlen($traceKeys)) ? "." : "";
            throw new InvalidArgumentException($traceKeys."(".join(", ", $unmappedKeys).") are passed but unknown/not allowed");
        }
        return $object;
    }

    /**
     * @param string $className
     * @return object
     * @param mixed $input
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function createObject(string $className, &$input) : object {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug("createObject $className",
            ["line" => __LINE__, "context" => $input]);
        if (class_exists($className)) {
            $reflect 						        = new ReflectionClass($className);
            if ($method = $reflect->getConstructor()) {
                $methodValues						= $this->getMethodValues($method, $input);
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
            $logger->error($message = "class $className does not exists");
            throw new RuntimeException($message);
        }
    }

    /**
     * @param object $object
     * @param mixed $input
     * @param bool $updateObject
     * @return array
     * @throws ReflectionException
     */
    private function updateObject(object $object, &$input, bool $updateObject) : array {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug("updateObject ".get_class($object),
            ["line" => __LINE__, "arguments" => $input]);
        $reflect                                    = new ReflectionClass($object);
        $unmappedKeys                               = [];
        foreach ($input as $inputKey => $inputValue) {
            if (is_string($inputKey)) {
                $logger->debug("updateObject inputKey: $inputKey",
                    ["line" => __LINE__, "inputValue" => $inputValue]);
                $setMethod                          = "set" . ucfirst($inputKey);
                if ($reflect->hasMethod($setMethod)) {
                    $logger->debug("updateObject use method: $setMethod",
                        ["line" => __LINE__]);
                    $method                         = $reflect->getMethod($setMethod);
                    $methodValues                   = $this->getMethodValues($method, $inputValue);
                    $logger->debug($this->getTraceKeys()." methodValues",
                        ["line" => __LINE__, "methodValues" => $methodValues]);
                    if (is_array($methodValues)) {
                        $method->invoke($object, ...$methodValues);
                    } else {
                        $method->invoke($object, $methodValues);
                    }
                } else {
                    $logger->debug("updateObject method $setMethod not found",
                        ["line" => __LINE__]);
                    $unmappedKeys[]                 = $inputKey;
                }
            }
        }
        return $unmappedKeys;
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $input
     * @return mixed
     * @throws LogicException
     * @throws ReflectionException
     */
    private function getMethodValues(ReflectionMethod $method, &$input) {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $methodValues								= [];
        $parameters 								= $method->getParameters();
        if ($method->isPublic()) {
            foreach ($parameters as $parameter) {
                $inputValue                         = $input;
                $aParameter                         = $this->annotationFactory->getAnnotationParameter($method, $parameter);
                $logger->debug("input, aParameter",
                    [
                        "line"                      => __LINE__,
                        "name"                      => $aParameter->getName(),
                        "isArray"                   => $aParameter->isArray(),
                        "isVariadic"                => $aParameter->isVariadic(),
                        "isBuiltIn"                 => $aParameter->isBuiltIn(),
                        "type"                      => $aParameter->getType(),
                        "declaringClass"            => $aParameter->getDeclaringClass(),
                    ]
                );
                $pKey                               = $aParameter->getName();
                $this->pushTraceKey($pKey);
                if (is_array($input)) {
                    if  (array_key_exists($pKey, $input)) {
                        $logger->debug($this->getTraceKeys()." found in input",
                            ["line" => __LINE__]);
                        $inputValue                 = $input[$pKey];
                        unset($input[$pKey]);
                    } else {
                        $logger->debug($this->getTraceKeys()." not found in input",
                            ["line" => __LINE__, 'input' => $input]);
                        if ($aParameter->isArray()) {
                            $logger->debug("parameter $pKey isArray");
                            // use input for parameter (should be the first one)
                            // ...unset input to prevent multiple usage
                            $input                  = null;
                        } else {
                            $inputValue             = null;
                        }
                    }
                }
                if (is_null($inputValue)) {
                    if ($aParameter->isDefaultValueAvailable()) {
                        $methodValue                = $aParameter->getDefaultValue();
                        $logger->debug($this->getTraceKeys()." use defaultValue",
                            ["line" => __LINE__]);
                    } elseif ($aParameter->isOptional()) {
                        $methodValue                = null;
                        $logger->debug($this->getTraceKeys(). " isOptional",
                            ["line" => __LINE__]);
                    } else {
                        throw new InvalidArgumentException($this->getTraceKeys() . " is required");
                    }
                } else {
                    if ($aParameter->isBuiltIn()) {
                        $methodValue                = $this->getBuiltInInputValue($aParameter, $inputValue);
                    } else {
                        if ($typeClassName = $aParameter->getType()) {
                            if (is_array($inputValue)) {
                                $inValues           = [];
                                foreach ($inputValue as $inValue) {
                                    $inValues[]     = $this->denormalize($typeClassName, $inValue);
                                }
                                $methodValue        = $inValues;
                            } else {
                                $methodValue        = $this->denormalize($typeClassName, $inputValue);
                            }
                        }
                        else {
                            // use input for parameter (should be the first one)
                            // ...unset input to prevent multiple usage
                            $methodValue            = $inputValue;
                            $input                  = null;
                        }
                    }
                }
                if ($aParameter->isVariadic() && is_array($methodValue)) {
                    $methodValues                   += $methodValue;
                } else {
                    $methodValues[]                 = $methodValue;
                }
                $logger->debug("methodValues after render parameter $pKey",
                    ["line" => __LINE__, "methodValues" => $methodValues]);
                $this->popTraceKey();
            }
            return $methodValues;
        } else {
            return null;
        }
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
     * @param AnnotationTypeInterface $parameter
     * @param mixed $input
     * @return mixed
     */
    private function getBuiltInInputValue(AnnotationTypeInterface $parameter, $input) {
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
            if ($inputType === "array") {
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
     */
    private function isInitializedObject(object $object) : void {
        $reflect                                    = new ReflectionClass($object);
        foreach ($reflect->getProperties() as $property) {
            $property->setAccessible(true);
            if (!$property->isInitialized($object)) {
                throw new InvalidArgumentException("property ".get_class($object)."::".$property->getName()." has not been initialized");
            }
        }
    }
}