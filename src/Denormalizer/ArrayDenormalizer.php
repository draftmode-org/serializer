<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Terrazza\Component\Serializer\DenormalizerInterface;

class ArrayDenormalizer implements DenormalizerInterface {
    use DenormalizerTrait;
    private AnnotationFactoryInterface $annotationFactory;
    CONST ACCESS_PROTECTED                          = 1;
    CONST ACCESS_PRIVATE                            = 2;
    private int $allowedAccess                      = 0;
    public function __construct(AnnotationFactoryInterface $annotationFactory) {
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
        if (is_object($className)) {
            $object                                 = clone $className;
        } elseif (is_string($className)) {
            if (!class_exists($className)) {
                throw new InvalidArgumentException("class $className does not exists/malformed");
            }
        } else {
            throw new InvalidArgumentException("class $className does not exists/malformed");
        }
        $reflect                                    = new ReflectionClass($className);
        if (is_string($className)) {
            $object                                 = $reflect->newInstanceWithoutConstructor();
        }
        if ($this->isBuiltIn(gettype($input))) {
            $object                                 = $this->initializeBuiltIn($object, $input);
        }
        elseif (is_array($input)) {
            foreach ($input as $inputKey => $inputValue) {
                $this->pushTraceKey($inputKey);
                if ($setter = $this->getSetterCallback($reflect, $inputKey)) {
                    $setter($object, $inputValue);
                }
                $this->popTraceKey();
            }
        }
        $this->isInitialized($object);
        return $object;
    }

    /**
     * @param object $object
     * @param mixed $input
     * @return object
     * @throws ReflectionException
     */
    private function initializeBuiltIn(object $object, $input) : object {
        $reflect                                    = new ReflectionClass($object);
        if ($constructor = $reflect->getConstructor()) {
            $values                                 = $this->getValuesForMethod($constructor, $input);
            return $reflect->newInstance(...$values);
        } else {
            throw new InvalidArgumentException($reflect->getName()." cannot be initialized, missing __constructor");
        }
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
     * @param ReflectionClass $reflect
     * @param string $propName
     * @return Closure|null
     */
    private function getSetterCallback(ReflectionClass $reflect, string $propName) :?Closure {
        $setMethod                                  = "set".ucfirst($propName);
        if ($reflect->hasMethod($setMethod)) {
            $method                                 = $reflect->getMethod($setMethod);
            $this->isAllowed($setMethod."() for ".$reflect->getName(),$method->isPublic(),$method->isProtected(),$method->isPrivate());
            $method->setAccessible(true);
            return function ($object, $inputValue) use ($method) {
                $values                             = $this->getValuesForMethod($method, $inputValue);
                $method->invoke($object, ...$values);
            };
        }
        return null;
    }

    /**
     * @param ReflectionMethod $method
     * @param mixed $inputValue
     * @return array
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    private function getValuesForMethod(ReflectionMethod $method, $inputValue) : array {
        $parameters                                 = $method->getParameters();
        $this->isAllowed($method->getName(). "() for ".$method->getDeclaringClass()->getName(),$method->isPublic(),$method->isProtected(),$method->isPrivate());
        if (count($parameters) === 1) {
            $parameter                              = array_shift($parameters);
            $value                                  = $this->getValue(
                $this->getParameterTypeByAnnotation($method, $parameter),
                $inputValue);
            $values                                 = [$value];
        } else {
            throw new InvalidArgumentException("count of parameters for method ".$method->getName()." has to be 1, given ".count($parameters));
        }
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