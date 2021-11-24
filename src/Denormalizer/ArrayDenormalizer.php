<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\DenormalizerInterface;

class ArrayDenormalizer implements DenormalizerInterface {
    use DenormalizerTrait;
    private LogInterface $logger;
    private AnnotationFactoryInterface $annotationFactory;
    CONST BUILT_IN_TYPES                            = ["int", "integer", "float", "double", "string", "DateTime"];

    public function __construct(LogInterface $logger, AnnotationFactoryInterface $annotationFactory) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = $annotationFactory->withBuiltInTypes(self::BUILT_IN_TYPES);
    }

    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function denormalize($className, $input) : object {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug((is_object($className) ?
            "object: " .basename(get_class($className)) :
            "class: " . $className),
            ["line" => __LINE__]);
        if (is_string($className)) {
            $object                                 = $this->createObject($className, $input);
            if (is_array($input) && count($input)) {
                $this->updateObject($object, $input, false);
            }
        } elseif (is_object($className)) {
            $object                                 = $className;
            $this->removeConstructorArguments($object, $input);
            if (is_array($input) && count($input)) {
                $this->updateObject($object, $input, true);
            }
        } else {
            throw new InvalidArgumentException("className is either an object nor a valid className");
        }
        return $object;
    }

    /**
     * @param $className
     * @param $input
     * @return object
     * @throws ReflectionException
     */
    private function createObject($className, &$input) : object {
        $reflect 									= new ReflectionClass($className);
        $method 									= $reflect->getConstructor();
        $methodValues								= $this->getMethodValues($method, $input);
        if (is_array($methodValues)) {
            $object                                 = $reflect->newInstance(...$methodValues);
        } else {
            $object                                 = $reflect->newInstance($methodValues);
        }
        return $object;
    }

    /**
     * @param object $className
     * @param $input
     */
    private function removeConstructorArguments(object $className, &$input) {
        $reflect 									= new ReflectionClass($className);
        $method 									= $reflect->getConstructor();
        if ($method && $method->isPublic() && is_array($input)) {
            foreach ($method->getParameters() as $parameter) {
                if (array_key_exists($parameter->getName(), $input)) {
                    unset($input[$parameter->getName()]);
                }
            }
        }
    }

    /**
     * @param object $object
     * @param $input
     * @param bool $updateObject
     * @throws ReflectionException
     */
    private function updateObject(object $object, $input, bool $updateObject) : void {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug(get_class($object),
            ["arguments" => $input, "line" => __LINE__]);
        $reflect                                    = new ReflectionClass($object);
        foreach ($input as $inputKey => $inputValue) {
            if (is_string($inputKey)) {
                $getMethod                          = "get" . ucfirst($inputKey);
                if ($updateObject && $reflect->hasMethod($getMethod)) {
                    $logger->debug("$getMethod",
                        ["arguments" => $inputValue, "line" => __LINE__]);
                    $method                         = $reflect->getMethod($getMethod);
                    if (in_array(gettype($inputValue), self::BUILT_IN_TYPES)) {
                        $logger->debug("inputValue is a builtIn");
                    } elseif (is_null($inputValue)) {
                        $returnType                 = $this->annotationFactory->getAnnotationReturnType($method);
                        if (!$returnType->isOptional()) {
                            $this->pushTraceKey($inputKey);
                            throw new InvalidArgumentException($this->getTraceKeys()." is required, given null");
                        }
                    }
                    else {
                        $getObject                  = $method->invoke($object);
                        $this->denormalize($getObject, $inputValue);
                        break;
                    }
                }
                $setMethod                          = "set" . ucfirst($inputKey);
                if ($reflect->hasMethod($setMethod)) {
                    $logger->debug("$setMethod",
                        ["arguments" => $inputValue, "line" => __LINE__]);
                    $method                         = $reflect->getMethod($setMethod);
                    $methodValue                    = [$inputKey => $inputValue];
                    $methodValues					= $this->getMethodValues($method, $methodValue);
                    $logger->debug("invoke $setMethod",
                        ["arguments" => print_r($methodValues,true), "line" => __LINE__]);
                    if (is_array($methodValues)) {
                        $method->invoke($object, ...$methodValues);
                    } else {
                        $method->invoke($object, $methodValues);
                    }
                }
            }
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param $input
     * @return array|null
     * @throws ReflectionException
     */
    private function getMethodValues(ReflectionMethod $method, &$input) :?array {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $methodValues								= [];
        $parameters 								= $method->getParameters();
        if ($method->isPublic()) {
            foreach ($parameters as $parameter) {
                $methodParameter                    = null;
                $aParameter                         = $this->annotationFactory->getAnnotationParameter($method, $parameter);
                $logger->debug("input, aParameter",
                    [
                        "line"                      => __LINE__,
                        "name"                      => $aParameter->getName(),
                        "isArray"                   => $aParameter->isArray(),
                        "isVariadic"                => $aParameter->isVariadic(),
                        "isBuiltIn"                 => $aParameter->isBuiltIn(),
                        "type"                      => $aParameter->getType(),
                        "inputIsArray"              => is_array($input),
                        "input"                     => $input,
                    ]
                );
                if (is_array($input)) {
                    $logger->debug("input is an array",
                        ["line" => __LINE__]);
                    if (array_key_exists($aParameter->getName(), $input)) {
                        $inputValue                 = $input[$aParameter->getName()];
                        unset($input[$aParameter->getName()]);
                        $methodParameter            = $aParameter;
                        $logger->debug("key ".$aParameter->getName()." exists, new input",
                            ["line" => __LINE__, "input" => $inputValue]);
                    } elseif ($aParameter->isVariadic() || $aParameter->isArray()) {
                        $inputValue                 = $input;
                        $methodParameter            = $aParameter;
                        $logger->debug("key ".$aParameter->getName()." does not exists, parameter isVariadic or isArray",
                            ["line" => __LINE__, "input" => $inputValue]);
                    }
                } elseif ($aParameter->isBuiltIn()) {
                    $inputValue                     = $input;
                    $methodParameter                = $aParameter;
                    $logger->debug("input is not an array, parameter isBuiltIn",
                        ["line" => __LINE__, "input" => $inputValue]);
                }
                if ($methodParameter) {
                    $this->pushTraceKey($methodParameter->getName());
                    if (is_array($inputValue)) {
                        $logger->debug("input is an array",
                            ["line" => __LINE__]);
                        $arrMethodValues            = [];
                        if ($methodParameter->isVariadic() || $methodParameter->isArray()) {
                            $logger->debug("parameter isVariadic or isArray",
                                ["line" => __LINE__]);
                            foreach ($inputValue as $singleInputValue) {
                                $arrMethodValues[]  = $this->getMethodValue($methodParameter, $singleInputValue);
                            }
                            if ($methodParameter->isVariadic()) {
                                $methodValues       += $arrMethodValues;
                            } else {
                                $methodValues[]     = $arrMethodValues;
                            }
                        } else {
                            $logger->debug("parameter not isVariadic, not isArray",
                                ["line" => __LINE__]);
                            $methodValues[]         = $this->getMethodValue($methodParameter, $inputValue);
                        }
                    } else {
                        $logger->debug("input not an array",
                            ["line" => __LINE__]);
                        $methodValues[]             = $this->getMethodValue($methodParameter, $inputValue);
                    }
                    $this->popTraceKey();
                } else {
                    $logger->debug("no input for parameter found",
                        ["line" => __LINE__]);
                    if ($aParameter->isOptional()) {
                        if ($aParameter->isDefaultValueAvailable()) {
                            $methodValues[]         = $aParameter->getDefaultValue();
                        } else {
                            $methodValues[]         = null;
                        }
                    } else {
                        throw new InvalidArgumentException($this->getTraceKeys() . " is required and missing");
                    }
                }
            }
            return $methodValues;
        } else {
            return null;
        }
    }

    /**
     * @param AnnotationParameter $parameter
     * @param $inputValue
     * @return mixed
     * @throws ReflectionException
     */
    private function getMethodValue(AnnotationParameter $parameter, $inputValue) {
        if (is_null($inputValue) && $parameter->isOptional()) {
            return null;
        }
        if ($parameter->isBuiltIn()) {
            return $this->getBuiltInValue($parameter, $inputValue);
        } elseif ($parameterType = $parameter->getType()) {
            return $this->denormalize($parameterType, $inputValue);
        } else {
            throw new InvalidArgumentException($this->getTraceKeys() . " could not be handled");
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
     * @param AnnotationParameter $parameter
     * @param mixed $input
     * @return mixed
     */
    private function getBuiltInValue(AnnotationParameter $parameter, $input) {
        $inputType 									= $this->getInputType($input);
        if ($parameter->isArray()) {
            if ($inputType === "array") {
                $inputValues						= [];
                foreach ($input as $inputValue) {
                    $inputValues[] 					= $this->getBuiltInInputValue($parameter, $inputValue);
                }
                return $inputValues;
            }
            else {
                throw new InvalidArgumentException("expected array, given $inputType");
            }
        } else {
            return $this->getBuiltInInputValue($parameter, $input);
        }
    }

    /**
     * @param AnnotationParameter $parameter
     * @param mixed $input
     * @return mixed
     */
    private function getBuiltInInputValue(AnnotationParameter $parameter, $input) {
        $inputType 									= $this->getInputType($input);
        $parameterType 								= $parameter->getType();
        if (is_null($input) && $parameter->isOptional()) {
            return null;
        }
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
            $this->popTraceKey();
            throw new InvalidArgumentException("expected type $parameterType, given $inputType, value: $input");
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