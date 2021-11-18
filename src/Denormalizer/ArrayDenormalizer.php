<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\DenormalizerInterface;

class ArrayDenormalizer implements DenormalizerInterface {
    use DenormalizerTrait;
    private LogInterface $logger;
    private AnnotationFactoryInterface $annotationFactory;
    CONST ACCESS_PROTECTED                          = 1;
    CONST ACCESS_PRIVATE                            = 2;
    private int $allowedAccess                      = 0;
    public function __construct(LogInterface $logger, AnnotationFactoryInterface $annotationFactory) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = $annotationFactory;
    }

    public function withAllowedAccess(int $allowedAccess) : self {
        $denormalizer                               = clone $this;
        $denormalizer->allowedAccess                = $allowedAccess;
        return $denormalizer;
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
        $logger = $this->logger->withMethod(__METHOD__);
        $logger->debug("class ".basename(is_object($className) ? get_class($className) : $className));
//var_dump("call denormalize", $input);
        if (is_object($className)) {
            $logger->debug("update class", ["arguments" => $input]);
            $object                                 = clone $className;
        } elseif (is_string($className)) {
            if (!class_exists($className)) {
                throw new InvalidArgumentException("class $className does not exists/malformed");
            }
            $logger->debug("create class", ["arguments" => $input]);
            //
            // initialize object via constructor
            // ...if input is an array
            //    remove found and used parameterKeys
            //
            $object                                 = $this->createObject($className, $input);
        } else {
            throw new InvalidArgumentException("class $className does not exists/malformed");
        }
        $this->updateObject($object, $input);
        /*if (is_array($input)) {
            $reflect                                = new ReflectionClass($className);
            foreach ($input as $inputKey => $inputValue) {
                var_dump("run for inputKey:$inputKey");
                var_dump($inputValue);
                $this->pushTraceKey($inputKey);
                if ($setter = $this->getSetterCallback($reflect, $inputKey)) {
                    $setter($object, $inputValue);
                }
                $this->popTraceKey();
            }
        }*/
        $this->isInitialized($object);
        return $object;
    }

    /**
     * @param object $object
     */
    private function isInitialized(object $object) : void {
        $reflect                                    = new ReflectionClass($object);
        foreach ($reflect->getProperties() as $property) {
            if (!$property->isInitialized($object)) {
                throw new InvalidArgumentException("property ".get_class($object)."::".$property->getName()." has not been initialized");
            }
        }
    }

    /**
     * @param class-string<T>
     * @param mixed $input
     * @return T
     * @template T
     * @return object
     * @throws ReflectionException
     */
    private function createObject($className, &$input) : object {
        $reflect                                    = new ReflectionClass($className);
        if ($constructor = $reflect->getConstructor()) {
            if ($constructor->isPublic()) {
                $values                             = $this->getValuesForConstructor($constructor, $input);
                return $reflect->newInstance(...$values);
            }
        }
        $reflect                                    = new ReflectionClass($className);
        return $reflect->newInstanceWithoutConstructor();
    }

    /**
     * @param object $object
     * @param mixed $input
     * @throws ReflectionException
     */
    private function updateObject(object $object, $input) : void {
        $reflect                                    = new ReflectionClass($object);
        if (is_array($input)) {
            foreach ($input as $inputKey => $inputValue) {
                if (is_string($inputKey)) {
                    $this->pushTraceKey($inputKey);
                    $setMethod                      = "set".ucfirst($inputKey);
                    if ($reflect->hasMethod($setMethod)) {
                        $method                     = $reflect->getMethod($setMethod);
                        $this->isAllowed($setMethod."() for ".$reflect->getName(),$method->isPublic(),$method->isProtected(),$method->isPrivate());
                        $method->setAccessible(true);
//var_dump("**** update", $inputValue);
                        $values                     = $this->getValueForMethod($method, $inputValue);
//var_dump("....response", $values);
                        if (is_array($values)) {
                            $method->invoke($object, ...$values);
                        } else {
                            $method->invoke($object, $values);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $input
     * @return mixed
     */
    private function getValueForMethod(ReflectionMethod $method, $input) {
        $parameters                                 = $method->getParameters();
        if (count($parameters) === 1) {
            $parameter                              = array_shift($parameters);
            $parameterType                          = $this->getParameterTypeByAnnotation($method, $parameter);
            $value                                  = $this->getValue($parameterType, $input);
            if ($parameter->isArray()) {
                return [$value];
            }
            return $value;
        } else {
            throw new InvalidArgumentException("multiple arguments for ".$method->getName()."() not implemented");
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $input
     * @return array
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    private function getValuesForConstructor(ReflectionMethod $method, &$input) : array {
        $parameters                                 = $method->getParameters();
        $values                                     = [];
        if (is_array($input)) {
            foreach ($parameters as $parameter) {
                $parameterName                      = $parameter->getName();
                $this->pushTraceKey($parameterName);
                $parameterType                      = $this->getParameterTypeByAnnotation($method, $parameter);
//var_dump($parameterName, $parameterType, $input);
                if (array_key_exists($parameterName, $input)) {
                    $inputValue                     = $input[$parameterName];
                    unset($input[$parameterName]);
                    $value                          = $this->getValue($parameterType, $inputValue);
//var_dump($value);
                    if ($parameter->isVariadic()) {
                        $values                     += $value;
                    } else {
                        $values[]                   = $value;
                    }
                    /*
                    if ($this->isBuiltIn($parameterType)) {
                        var_dump($inputValue);
                        if ($this->isBuiltIn(gettype($inputValue))) {
                            var_dump("case *******************".__LINE__);
                            $values[]               = $inputValue;
                        }
                        elseif (is_array($inputValue)) {
                            var_dump("case *******************".__LINE__);
                            var_dump($this->getValue($parameterType, $inputValue));
                            $parameterValue         = [];
                            foreach ($inputValue as $subInputValue) {
                                $parameterValue[]   = $this->getValue($parameterType, $subInputValue);
                            }
                            var_dump($parameterValue);
                            if ($parameter->isVariadic()) {
                                $values             += $parameterValue;
                            } else {
                                $values[]           = $parameterValue;
                            }
                        } else {
                            var_dump("case *******************".__LINE__);
                            throw new InvalidArgumentException("parameterType isBuiltIn, inputValue (!builtIn, !array)");
                        }
                    } elseif ($parameterType === "array") {
                        $values[]                   = $this->getValue($parameterType, $inputValue);
                    } else {
                        throw new InvalidArgumentException("not builtIn, not array");
                    }*/
                } else {
                    if ($parameter->isOptional() && $parameter->isDefaultValueAvailable()) {
                        $values[]                   = $parameter->getDefaultValue();
                    } else {
                        throw new InvalidArgumentException("not implemented...");
                    }
                }
                $this->popTraceKey();
            }
        } elseif ($this->isBuiltIn(gettype($input))) {
            $values[]                               = $input;
            //throw new InvalidArgumentException("input is not an array, but builtIn...");
        }
//var_dump($values);
        return $values;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    private function getValue(string $propertyType, $inputValue) {
        if ($this->isBuiltIn($propertyType)) {
            if (is_array($inputValue)) {
                $arrayArgs              = [];
                foreach ($inputValue as $singleInputValue) {
                    $arrayArgs[]        = $this->getApprovedBuiltInValue($propertyType, $singleInputValue);
                }
                return $arrayArgs;
            } else {
                return $this->getApprovedBuiltInValue($propertyType, $inputValue);
            }
        }
        elseif (is_array($inputValue)) {
            if ($propertyType === "array") {
                return $inputValue;
            } else {
                $arrayArgs              = [];
                foreach ($inputValue as $singleInputValue) {
                    $arrayArgs[]        = $this->denormalize($propertyType, $singleInputValue);
                }
                return $arrayArgs;
            }
        } else {
            throw new InvalidArgumentException("type $propertyType for ".$this->getTraceKeys()." cannot be resolved");
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param ReflectionParameter $property
     * @return string|null
     */
    private function getParameterTypeByAnnotation(ReflectionMethod $method, ReflectionParameter $property) :?string {
        if ($annotationType = $this->annotationFactory->getParameterTypeByAnnotation($method, $property->getName())) {
            if ($this->isBuiltIn($annotationType)) {
                return $annotationType;
            } elseif ($annotationTypeClass = $this->annotationFactory->getClassName($property->getDeclaringClass()->getName(), $annotationType)) {
                return $annotationTypeClass;
            } else {
                return $annotationType;
            }
        } elseif ($property->getType()) {
            return $property->getType()->getName();
        }
        return null;
    }
}