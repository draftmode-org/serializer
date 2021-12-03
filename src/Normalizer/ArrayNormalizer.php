<?php

namespace Terrazza\Component\Serializer\Normalizer;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationProperty;
use Terrazza\Component\Serializer\NameConverterInterface;
use Terrazza\Component\Serializer\NormalizerInterface;
use Terrazza\Component\Serializer\TraceKeyTrait;
use Throwable;

class ArrayNormalizer implements NormalizerInterface {
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
     * @param array $nameConverter
     * @return NormalizerInterface
     */
    public function withNameConverter(array $nameConverter) : NormalizerInterface {
        $normalizer                                 = clone $this;
        $normalizer->nameConverter                  = $nameConverter;
        return $normalizer;
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
        $logger->debug("$attributeName in class ".$refClass->getName(),
            ["line" => __LINE__]);
        $refProperty                                = $refClass->getProperty($attributeName);
        $property                                   = $this->annotationFactory->getAnnotationProperty($refProperty);
        $refProperty->setAccessible(true);
        if ($refProperty->isInitialized($object)) {
            $logger->debug("property", [
                "line"              => __LINE__,
                "name"              => $property->getName(),
                "isArray"           => $property->isArray(),
                "isBuiltIn"         => $property->isBuiltIn(),
                "type"              => $property->getType(),
            ]);
            $attributeValue                         = $refProperty->getValue($object);
            if (is_null($attributeValue)) {
                return null;
            }
            if ($property->isBuiltIn()) {
                return $attributeValue;
            } elseif ($propertyTypeClass = $property->getType()) {
                /** @var class-string $propertyTypeClass */
                if ($property->isArray()) {
                    $attributeValues                = [];
                    foreach ($attributeValue as $singleAttributeValue) {
                        $attributeValues[]          = $this->getAttributeValueByTypeClass($propertyTypeClass, $singleAttributeValue);
                    }
                    return $attributeValues;
                } else {
                    return $this->getAttributeValueByTypeClass($propertyTypeClass, $attributeValue);
                }
            } else {
                throw new RuntimeException($this->getTraceKeys()." propertyValue for propertyType cannot be resolved");
            }
        } else {
            throw new RuntimeException($this->getTraceKeys()." property is not initialized");
        }
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @param class-string $propertyTypeClass
     * @param mixed $attributeValue
     * @return mixed
     * @throws ReflectionException
     */
    private function getAttributeValueByTypeClass(string $propertyTypeClass, $attributeValue) {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        if ($nameConverterClass = $this->getNameConverterClass($propertyTypeClass)) {
            $logger->debug("nameConverterClass for property found",
                ["line" => __LINE__, 'className' => $propertyTypeClass]);
            if (class_exists($nameConverterClass)) {
                $converter                          = new ReflectionClass($nameConverterClass);
                if ($converter->implementsInterface(NameConverterInterface::class)) {
                    /** @var NameConverterInterface $convertClass */
                    $convertClass                   = $converter->newInstance($attributeValue);
                    try {
                        return $convertClass->getValue();
                    } catch (Throwable $exception) {
                        $errorCode                  = (int)$exception->getCode();
                        throw new RuntimeException("getValue() for nameConvertClass $propertyTypeClass failure: " . $exception->getMessage(), $errorCode, $exception);
                    }
                } else {
                    throw new RuntimeException("$nameConverterClass does not implement " . NameConverterInterface::class);
                }
            } else {
                throw new RuntimeException("$nameConverterClass does not exists");
            }
        } else {
            $logger->debug("nameConverterClass for property not found",
                ["line" => __LINE__, 'className' => $propertyTypeClass]);
            return $this->normalize($attributeValue);
        }
    }

    /**
     * @param class-string $fromType
     * @return class-string|null
     * @throws ReflectionException
     */
    private function getNameConverterClass(string $fromType) :?string {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        if (array_key_exists($fromType, $this->nameConverter)) {
            $logger->debug("nameConverter for $fromType found", ["line" => __LINE__]);
            return $this->nameConverter[$fromType];
        } else {
            $logger->debug("no nameConverter for $fromType", ["line" => __LINE__]);
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
                $logger->debug("method $methodName starts with $needle",
                    ["line" => __LINE__]);
            }
            elseif ($this->_str_starts_with($methodName, $needle = 'has')) {
                $attributeName                      = substr($methodName, 3);
                $logger->debug("method $methodName starts with $needle",
                    ["line" => __LINE__]);
            } elseif ($this->_str_starts_with($methodName, $needle = 'is')) {
                $attributeName                      = substr($methodName, 2);
                $logger->debug("method $methodName starts with $needle",
                    ["line" => __LINE__]);
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
                $logger->debug("skip property $propertyName, already found as method",
                    ["line" => __LINE__]);
                continue;
            }
            if (!$property->isPublic()) {
                $logger->debug("skip property $propertyName, is not public",
                    ["line" => __LINE__]);
                continue;
            }
            if ($property->isStatic()) {
                $logger->debug("skip property $propertyName, is static",
                    ["line" => __LINE__]);
                continue;
            }
            $attributeName                          = $property->getName();
            $attributes[$attributeName]             = true;
        }

        return $attributes;
    }
}