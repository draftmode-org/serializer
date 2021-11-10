<?php

namespace Terrazza\Component\Serializer\Denormalizer;
use ReflectionMethod;
use ReflectionProperty;

interface AnnotationFactoryInterface {
    public function extractTypeFromAnnotation(string $annotation) :?string;
    public function getPropertyTypeByAnnotation(ReflectionProperty $property) :?string;
    public function getParameterTypeByAnnotation(ReflectionMethod $method, string $parameterName) :?string;
    public function getClassName(string $parentClass, string $findClass) :?string;
}