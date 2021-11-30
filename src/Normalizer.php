<?php

namespace Terrazza\Component\Serializer;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use stdClass;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;

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
                if ($property->isBuiltIn()) {
                    $logger->debug("property $attributeName is builtIn", ["line" => __LINE__]);
                    return $attributeValue;
                } elseif ($propertyType = $property->getType()) {
                    $logger->debug("property $attributeName withType ".$propertyType, ["line" => __LINE__]);
                    if ($nameConverterClass = $this->getNameConverterClass($propertyType)) {
                        $logger->debug("nameConverterClass for property $attributeName found", ["line" => __LINE__]);
                        $converter                  = new ReflectionClass($nameConverterClass);
                        if ($converter->implementsInterface(NameConverterInterface::class)) {
                            /** @var NameConverterInterface $convertClass */
                            $convertClass           = $converter->newInstance($attributeValue);
                            return $convertClass->getValue();
                        } else {
                            throw new RuntimeException("$nameConverterClass does not implement ".NameConverterInterface::class);
                        }
                    } else {
                        $logger->debug("no nameConverterClass for property $attributeName found", ["line" => __LINE__]);
                        return $this->normalize($attributeValue);
                    }
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

    private function _str_starts_with(string $haystack, string $needle) :?string {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }

    private function isAllowedAttribute() : bool {
        return true;
    }

    /**
     * @param object $object
     * @return array
     */
    private function getAttributes(object $object) : array {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        if (stdClass::class === get_class($object)) {
            return array_keys((array) $object);
        }

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
            if ($attributeName && !$refClass->hasProperty($attributeName)) {
                $attributeName                      = lcfirst($attributeName);
            }
            if ($attributeName !== null && $this->isAllowedAttribute()) {
                $attributes[$attributeName]         = true;
            }
        }

        foreach ($refClass->getProperties() as $property) {
            $propertyName                           = $property->getName();
            if (!$property->isPublic()) {
                $logger->debug("skip property $propertyName", ["line" => __LINE__]);
                continue;
            }
            if ($property->isStatic() || !$this->isAllowedAttribute()) {
                $logger->debug("skip property $propertyName", ["line" => __LINE__]);
                continue;
            }
            $attributeName                          = $property->getName();
            $attributes[$attributeName]             = true;
        }

        return $attributes;
    }
}