<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Terrazza\Component\ReflectionClass\ClassName\ReflectionClassClassNameException;
use Terrazza\Component\ReflectionClass\ClassName\ReflectionClassClassNameInterface;
use Terrazza\Component\Serializer\DenormalizerInterface;

class ArrayDenormalizer implements DenormalizerInterface {
    use DenormalizerTrait;
    private ReflectionClassClassNameInterface $reflectionClassClassName;
    public function __construct(ReflectionClassClassNameInterface $reflectionClassClassName) {
        $this->reflectionClassClassName             = $reflectionClassClassName;
    }

    /**
     * @param object|string $class
     * @param mixed $input
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ReflectionClassClassNameException
     * @return object
     */
    public function denormalize($class, $input) : object {
        $rClass                                     = new ReflectionClass($class);
        $inputType                                  = gettype($input);
        $args                                       = [];
        if ($constructor = $rClass->getConstructor()) {
            foreach ($constructor->getParameters() as $parameter) {
                $key                               = $parameter->name;
                $this->pushTraceKey($key);
                $parameterType                      = $this->getParameterType($parameter, $constructor);
                if ($this->isBuiltIn($inputType)) {
                    $args[]                         = $this->getApprovedBuiltInValue($parameterType, $input);
                }
                elseif (array_key_exists($key, $input)) {
                    $inputValue                     = $input[$key];
                    if ($parameterType) {
                        if ($this->isBuiltIn($parameterType)) {
                            if ($parameter->isVariadic()) {
                                if (is_array($inputValue)) {
                                    $args           += array_values($inputValue);
                                }
                            } else {
                                if (is_array($inputValue)) {
                                    $args[]         = array_map(function ($value) use ($parameterType) {
                                        return $this->getApprovedBuiltInValue($parameterType, $value);
                                    }, $inputValue);
                                } else {
                                    $args[]         = $this->getApprovedBuiltInValue($parameterType, $inputValue);
                                }
                            }
                        } else {
                            if (is_array($inputValue)) {
                                if ($parameterType === "array") {
                                    $args[]         = $inputValue;
                                } else {
                                    $arrayArgs      = [];
                                    foreach ($inputValue as $value) {
                                        $arrayArgs[]= $this->denormalize($parameterType, $value);
                                    }
                                    if ($parameter->isVariadic()) {
                                        $args       += array_values($arrayArgs);
                                    } else {
                                        $args[]     = $arrayArgs;
                                    }
                                }
                            } else {
                                $args[]             = $this->denormalize($parameterType, $value);
                            }
                        }
                    } else {
                        throw new ReflectionException("parameter ".$this->getTraceKeys()." does not provide any type");
                    }
                } else {
                    if ($parameter->allowsNull()) {
                        $args[]                     = null;
                    } elseif ($parameter->isDefaultValueAvailable() && $parameter->getDefaultValue()) {
                        $args[]                     = $parameter->getDefaultValue();
                    } else {
                        throw new InvalidArgumentException("argument ".$this->getTraceKeys()." is missing, has no default and is not nullable");
                    }
                }
                $this->popTraceKey();
            }
        } else {
            throw new InvalidArgumentException("className $class does not have any constructor");
        }
        if (count($args)) {
            return $rClass->newInstanceArgs($args);
        } else {
            return $rClass->newInstanceWithoutConstructor();
        }
    }

    /**
     * @param ReflectionParameter $parameter
     * @param ReflectionMethod $method
     * @return string|null
     * @throws ReflectionClassClassNameException
     */
    private function getParameterType(ReflectionParameter $parameter, ReflectionMethod $method) :?string {
        $parameterName                              = $parameter->name;
        if ($parameter->getType()) {
            $parameterType                          = $parameter->getType()->getName();
            if ($this->isBuiltIn($parameterType)) {
                return $parameterType;
            } else {
                if ($annotationType = $this->getParameterTypeByAnnotation($method, $parameterName)) {
                    return $annotationType;
                } else {
                    return $parameterType;
                }
            }
        } else {
            return $this->getParameterTypeByAnnotation($method, $parameterName);
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param string $parameterName
     * @return string|null
     * @throws ReflectionClassClassNameException
     */
    private function getParameterTypeByAnnotation(ReflectionMethod $method, string $parameterName) :?string {
        if (preg_match('/@param\\s+(\\S+)\\s+\\$' . $parameterName . '/', $method->getDocComment(), $matches)) {
            if ($annotationType = $this->extractTypeFromAnnotation($matches[1])) {
                if ($this->isBuiltIn($annotationType)) {
                    return $annotationType;
                }
                $propertyClassName                      = $method->getDeclaringClass()->getName();
                if ($propertyTypeClassName = $this->reflectionClassClassName->getClassName($propertyClassName, $annotationType)) {
                    return $propertyTypeClassName;
                }
            }
        }
        return null;
    }
}