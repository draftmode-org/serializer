<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use stdClass;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Throwable;

class Normalizer implements NormalizerInterface {
    use TraceKeyTrait;
    private LogInterface $logger;
    private AnnotationFactoryInterface $annotationFactory;
    private array $nameConverter;

    public function __construct(LogInterface $logger, AnnotationFactoryInterface $annotationFactory, array $nameConverter=null) {
        $this->logger                               = $logger;
        $this->annotationFactory                    = $annotationFactory;
        $this->nameConverter                        = $nameConverter ?? [];
    }

    /**
     * @param object $object
     * @return array
     * @throws ReflectionException
     */
    public function normalize(object $object) : array {
        $values                                     = [];
        foreach ($this->getAttributes($object) as $attributeName => $attributeValid) {
            if ($attributeValid === true) {
                $this->pushTraceKey($attributeName);
                $values[$attributeName]             = $this->getAttributeValue($object, $attributeName);
                $this->popTraceKey();
            }
        }
        return $values;
    }

    /**
     * @param object $object
     * @param string $attributeName
     * @return mixed
     * @throws ReflectionException
     */
    private function getAttributeValue(object $object, string $attributeName) {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $refClass                                   = new ReflectionClass($object);
        $logger->debug("$attributeName in class ".$refClass->getName(), ["line" => __LINE__]);
        if ($refClass->hasProperty($attributeName)) {
            $refProperty                            = $refClass->getProperty($attributeName);
            $property                               = $this->annotationFactory->getAnnotationProperty($refProperty);
            $refProperty->setAccessible(true);
            if ($refProperty->isInitialized($object)) {
                $attributeValue                     = $refProperty->getValue($object);
                if (is_null($attributeValue)) {
                    if ($property->isOptional()) {
                        return null;
                    } else {
                        throw new InvalidArgumentException($this->getTraceKeys()." expected value, given null");
                    }
                }
                if ($property->isBuiltIn()) {
                    $logger->debug("property $attributeName is builtIn", ["line" => __LINE__, "isArray" => $property->isArray()]);
                    if ($propertyType = $property->getType()) {
                        if (is_array($attributeValue)) {
                            $attributeValues        = [];
                            foreach ($attributeValue as $singleAttributeValue) {
                                $attributeValues[]  = $this->getAttributeValueByType($propertyType, $singleAttributeValue);
                            }
                            return $attributeValues;
                        } else {
                            throw new InvalidArgumentException($this->getTraceKeys()." expected array given ".gettype($attributeValue));
                        }
                    } else {
                        return $attributeValue;
                    }
                } elseif ($propertyType = $property->getType()) {
                    $logger->debug("property $attributeName withType ".$propertyType, ["line" => __LINE__]);
                    return $this->getAttributeValueByType($propertyType, $attributeValue);
                } else {
                    throw new RuntimeException($this->getTraceKeys()." property could not be resolved");
                }
            } else {
                throw new RuntimeException($this->getTraceKeys()." property is not initialized");
            }
        } else {
            throw new RuntimeException($this->getTraceKeys()." property does not exists");
        }
    }

    /**
     * @param string $propertyType
     * @param mixed $attributeValue
     * @return mixed
     * @throws ReflectionException
     */
    private function getAttributeValueByType(string $propertyType, $attributeValue) {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        if ($nameConverterClass = $this->getNameConverterClass($propertyType)) {
            $logger->debug("nameConverterClass for property found", ["line" => __LINE__]);
            $converter                  = new ReflectionClass($nameConverterClass);
            if ($converter->implementsInterface(NameConverterInterface::class)) {
                /** @var NameConverterInterface $convertClass */
                $convertClass           = $converter->newInstance($attributeValue);
                try {
                    return $convertClass->getValue();
                } catch (Throwable $exception) {
                    throw new RuntimeException("getValue() for nameConvertClass $propertyType failure: ".$exception->getMessage(), $exception->getCode(), $exception);
                }
            } else {
                throw new RuntimeException("$nameConverterClass does not implement ".NameConverterInterface::class);
            }
        } else {
            $logger->debug("no nameConverterClass for property found", ["line" => __LINE__]);
            return $this->normalize($attributeValue);
        }
    }

    /**
     * @param string $fromType
     * @return string|null
     * @throws ReflectionException
     */
    private function getNameConverterClass(string $fromType) :?string {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $logger->debug("search for $fromType", ["line" => __LINE__]);
        if (array_key_exists($fromType, $this->nameConverter)) {
            $logger->debug("$fromType found", ["line" => __LINE__]);
            return $this->nameConverter[$fromType];
        } else {
            $converter                              = new ReflectionClass($fromType);
            if ($parentClass = $converter->getParentClass()) {
                return $this->getNameConverterClass($parentClass->getName());
            } else {
                return null;
            }
        }
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function _str_starts_with(string $haystack, string $needle) : bool {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    /**
     * @param object $object
     * @return array
     */
    private function getAttributes(object $object) : array {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        /*
        if (stdClass::class === get_class($object)) {
            return array_keys((array) $object);
        }
        */
        $attributes                                 = [];
        $refClass                                   = new ReflectionClass($object);
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName                             = $method->getName();
            if ($method->isStatic() ||
                $method->isConstructor() ||
                $method->isDestructor()
            ) {
                $logger->debug("skip method $methodName", ["line" => __LINE__]);
                continue;
            }
            $attributeName                          = null;
            if ($this->_str_starts_with($methodName, $needle = 'get')) {
                $attributeName                      = substr($methodName, 3);
                $logger->debug("method $methodName starts with $needle", ["line" => __LINE__]);
            }
            elseif ($this->_str_starts_with($methodName, $needle = 'has')) {
                $attributeName                      = substr($methodName, 3);
                $logger->debug("method $methodName starts with $needle", ["line" => __LINE__]);
            } elseif ($this->_str_starts_with($methodName, $needle = 'is')) {
                $attributeName                      = substr($methodName, 2);
                $logger->debug("method $methodName starts with $needle", ["line" => __LINE__]);
            }
            if ($attributeName !== null) {
                $attributeName                      = lcfirst($attributeName);
                if ($refClass->hasProperty($attributeName)) {
                    $attributes[$attributeName]     = true;
                }
            }
        }

        foreach ($refClass->getProperties() as $property) {
            $propertyName                           = $property->getName();
            if (array_key_exists($propertyName, $attributes)) {
                $logger->debug("skip property $propertyName, already found as method", ["line" => __LINE__]);
                continue;
            }
            if (!$property->isPublic()) {
                $logger->debug("skip property $propertyName, is not public", ["line" => __LINE__]);
                continue;
            }
            if ($property->isStatic()) {
                $logger->debug("skip property $propertyName, is static", ["line" => __LINE__]);
                continue;
            }
            $attributeName                          = $property->getName();
            $attributes[$attributeName]             = true;
        }

        return $attributes;
    }
}