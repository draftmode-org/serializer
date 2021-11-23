<?php

namespace Terrazza\Component\Serializer\Denormalizer;
use ReflectionMethod;
use ReflectionParameter;

interface AnnotationFactoryInterface {
    /**
     * @param array $builtInTypes
     * @return AnnotationFactoryInterface
     */
    public function withBuiltInTypes(array $builtInTypes) : AnnotationFactoryInterface;

    /**
     * @param ReflectionMethod $method
     * @param ReflectionParameter $parameter
     * @return AnnotationParameter
     */
    public function getAnnotationParameter(ReflectionMethod $method, ReflectionParameter $parameter) : AnnotationParameter;

    /**
     * @param ReflectionMethod $method
     * @return AnnotationReturnType
     */
    public function getAnnotationReturnType(ReflectionMethod $method) : AnnotationReturnType;

/*
    public function extractTypeFromAnnotation(string $annotation) :?string;
    public function getParameterTypeByAnnotation(ReflectionMethod $method, string $parameterName) :?string;
    public function getClassName(string $parentClass, string $findClass) :?string;
*/
}