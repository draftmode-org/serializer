<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Terrazza\Component\Annotation\AnnotationFactory;
use Terrazza\Component\Annotation\IAnnotationFactory;
use Terrazza\Component\Annotation\IAnnotationType;

class Denormalizer implements IDenormalizer {
    use TraceKeyTrait;
    private LoggerInterface $logger;
    private IAnnotationFactory $annotationFactory;
    private bool $restrictUnInitialized=false;
    private bool $restrictArguments=false;

    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = new AnnotationFactory($logger);
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param class-string<T> $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalize(string $className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) : object {
        if ($restrictUnInitialized)
            $this->restrictUnInitialized            = true;
        if ($restrictArguments)
            $this->restrictArguments                = true;
        $this->logger->debug("denormalize class $className");
        $unmappedKeys                               = [];
        $object                                     = $this->initializeObject($className, $input);
        if (is_array($input) && count($input)) {
            $unmappedKeys                           = $this->updateObject($object, $input);
        }
        if ($this->restrictUnInitialized) {
            $this->isInitializedObject($object);
        }
        if ($this->restrictArguments && is_array($unmappedKeys) && count($unmappedKeys)) {
            throw new InvalidArgumentException($this->getRestrictedArgumentsMessage($unmappedKeys));
        }
        return $object;
    }

    /**
     * @param object $object
     * @param string $methodName
     * @param mixed $input
     * @param bool $restrictArguments
     * @return mixed
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalizeMethodValues(object $object, string $methodName, $input, bool $restrictArguments=false) {
        $this->restrictUnInitialized                = true;
        if ($restrictArguments)
            $this->restrictArguments                = true;
        $reflect 						            = new ReflectionClass($object);
        if ($reflect->hasMethod($methodName)) {
            $method                                 = $reflect->getMethod($methodName);
            $methodValues                           = $this->getMethodValues($method, $input);
            if ($restrictArguments && is_array($input) && count($input)) {
                $unmappedKeys                       = array_keys($input);
                throw new InvalidArgumentException($this->getRestrictedArgumentsMessage($unmappedKeys));
            }
            return $methodValues;
        } else {
            $this->logger->error($message = "method $methodName for class ".$reflect->getName()." does not exists");
            throw new RuntimeException($message);
        }
    }

    /**
     * @param array $unmappedKeys
     * @return string
     */
    private function getRestrictedArgumentsMessage(array $unmappedKeys) : string {
        $traceKeys                                  = $this->getTraceKeys();
        $traceKeys                                  .= (strlen($traceKeys)) ? "." : "";
        $message                                    = join(", ", $unmappedKeys);
        if (count($unmappedKeys) > 1) {
            $message                                = "(".$message.") are";
        } else {
            $message                                = $message." is";
        }
        return $traceKeys.$message." passed but unknown/not allowed";
    }

    /**
     * @param class-string<T> $className
     * @return T
     * @template T of object
     * @param mixed $input
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function initializeObject(string $className, &$input) : object {
        $this->logger->debug("initialize ".basename($className),
            ["arguments" => $input]);
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
            $message                                = "class $className does not exists";
            throw new RuntimeException($message);
        }
    }

    /**
     * @param object $object
     * @param array $input
     * @return array|null
     * @throws ReflectionException
     */
    private function updateObject(object $object, array &$input) :?array {
        $this->logger->debug("updateObject ".basename(get_class($object)),
            ["arguments" => $input]);
        $reflect                                    = new ReflectionClass($object);
        $unmappedKeys                               = [];
        foreach ($input as $inputKey => $inputValue) {
            if (is_string($inputKey)) {
                $this->logger->debug("updateObject inputKey: $inputKey",
                    ["inputValue" => $inputValue]);
                $setMethodName                      = "set" . ucfirst($inputKey);
                if ($reflect->hasMethod($setMethodName)) {
                    $this->logger->debug("updateObject use method: $setMethodName");
                    $setMethod                      = $reflect->getMethod($setMethodName);
                    $parameters                     = $setMethod->getParameters();
                    //
                    // getMethod has only one parameter
                    // => forward inputValues wrapped to first parameter key to get method
                    //
                    if (count($parameters) === 1) {
                        $parameter                  = array_shift($parameters);
                        //
                        // in case of inputValue === null
                        // ==> protect isOptional
                        //
                        if (is_null($inputValue)) {
                            if ($parameter->isOptional()) {
                                $setValues          = null;
                            } else {
                                throw new InvalidArgumentException($this->getTraceKeys()." is required, given null");
                            }
                        } else {
                            $parameterKey           = $parameter->getName();
                            $setValues              = [$parameterKey => $inputValue];
                        }
                    //
                    // => forward inputValues 1:1 to get method
                    //
                    } else {
                        $setValues                  = $inputValue;
                    }
                    if (is_null($setValues)) {
                        $methodValues               = $setValues;
                    } else {
                        $methodValues               = $this->getMethodValues($setMethod, $setValues);
                    }
                    if (is_null($methodValues)) {
                        $setMethod->invoke($object, null);
                    }
                    elseif (is_array($methodValues)) {
                        $this->logger->debug("...set", $methodValues);
                        $setMethod->invoke($object, ...$methodValues);
                    } else {
                        $setMethod->invoke($object, $methodValues);
                    }
                } else {
                    $this->logger->debug("method $setMethodName not found");
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
        $methodValues								= [];
        $this->logger->debug("original input",
            ["arguments" => $input]);
        $inputValues                                = $this->mapMethodValues($method, $input);
        $this->logger->debug("mapped input",
            ["arguments" => $inputValues]);
        $parameters 							    = $method->getParameters();
        foreach ($parameters as $parameter) {
            $aParameter                             = $this->annotationFactory->getAnnotationParameter($method, $parameter);
            $this->logger->debug("input, aParameter", [
                    "name"                          => $aParameter->getName(),
                    "isArray"                       => $aParameter->isArray(),
                    "isVariadic"                    => $aParameter->isVariadic(),
                    "isBuiltIn"                     => $aParameter->isBuiltIn(),
                    "type"                          => $aParameter->getType(),
                    "declaringClass"                => $aParameter->getDeclaringClass(),
                ]);
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
                    $this->logger->debug($this->getTraceKeys()." use defaultValue",
                        ['methodValue' => $methodValue]);
                } elseif ($aParameter->isOptional()) {
                    $methodValue                    = null;
                    $this->logger->debug($this->getTraceKeys(). " isOptional",
                        ['methodValue' => $methodValue]);
                } else {
                    throw new InvalidArgumentException("argument ".$this->getTraceKeys() . " is required");
                }
            } else {
                if ($aParameter->isBuiltIn()) {
                    $methodValue                    = $this->getBuiltInInputValue($aParameter, $inputValue);
                } else {
                    if ($typeClassName = $aParameter->getType()) {
                        /** @var class-string $typeClassName */
                        if ($aParameter->isArray()) {
                            if (!is_array($inputValue)) {
                                throw new InvalidArgumentException("argument ".$this->getTraceKeys()." expected type array, given ".gettype($inputValue));
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
            throw new InvalidArgumentException("argument $traceKey expected type $parameterType, given $inputType");
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