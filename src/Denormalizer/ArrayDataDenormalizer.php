<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Terrazza\Component\Serializer\DenormalizerInterface;

class ArrayDataDenormalizer implements DenormalizerInterface {
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
     * @param object|string $class
     * @param mixed $input
     * @return object
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function denormalize($class, $input) : object {
        $reflect                                    = new ReflectionClass($class);
        if (is_object($class)) {
            $object                                 = clone $class;
        } elseif (is_string($class)) {
            if (class_exists($class)) {
                $object                             = $reflect->newInstanceWithoutConstructor();
            } else {
                throw new InvalidArgumentException("class $class does not exists");
            }
        } else {
            throw new InvalidArgumentException("$class has to be an object or valid className, given ".gettype($class));
        }
        if (is_array($input)) {
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
            $this->isAllowed($reflect->getName()."->$setMethod()",$method->isPublic(),$method->isProtected(),$method->isPrivate());
            $method->setAccessible(true);
            return function ($object, $value) use ($method) {
                $method->invoke($object, $value);
            };
        }
        if ($reflect->hasProperty($propName)) {
            $prop                                   = $reflect->getProperty($propName);
            $this->isAllowed($reflect->getName()."\\$propName",$prop->isPublic(),$prop->isProtected(),$prop->isPrivate());
            $prop->setAccessible(true);
            return function ($object, $inputValue) use ($prop) {
                $value                              = $this->getValue($this->getPropertyTypeByAnnotation($prop), $inputValue);
                $prop->setValue($object, $value);
            };
        }
        return null;
    }

    /**
     * @throws ReflectionException
     * @return mixed
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
     * @param ReflectionProperty $property
     * @return string|null
     */
    private function getPropertyTypeByAnnotation(ReflectionProperty $property) :?string {
        if ($annotationType = $this->annotationFactory->getPropertyTypeByAnnotation($property)) {
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