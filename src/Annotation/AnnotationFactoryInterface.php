<?php

namespace Terrazza\Component\Serializer\Annotation;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

interface AnnotationFactoryInterface {
    /**
     * @param array $builtInTypes
     * @return AnnotationFactoryInterface
     */
    public function withBuiltInTypes(array $builtInTypes) : AnnotationFactoryInterface;

    /**
     * @param string $type
     * @return bool
     */
    public function isBuiltInType(string $type) : bool;

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

    /**
     * @param ReflectionProperty $property
     * @return AnnotationProperty
     */
    public function getAnnotationProperty(ReflectionProperty $property) : AnnotationProperty;

/*
    public function extractTypeFromAnnotation(string $annotation) :?string;
    public function getParameterTypeByAnnotation(ReflectionMethod $method, string $parameterName) :?string;
    public function getClassName(string $parentClass, string $findClass) :?string;
*/
}