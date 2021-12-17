<?php

namespace Terrazza\Component\Serializer\Annotation;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

interface IAnnotationFactory {
    /**
     * @param array $builtInTypes
     * @return IAnnotationFactory
     */
    public function withBuiltInTypes(array $builtInTypes) : IAnnotationFactory;

    /**
     * @param string $type
     * @return bool
     */
    public function isBuiltInType(string $type) : bool;

    /**
     * @param ReflectionMethod $refMethod
     * @param ReflectionParameter $refParameter
     * @return AnnotationParameter
     */
    public function getAnnotationParameter(ReflectionMethod $refMethod, ReflectionParameter $refParameter) : AnnotationParameter;

    /**
     * @param ReflectionMethod $method
     * @return AnnotationReturnType
     */
    public function getAnnotationReturnType(ReflectionMethod $method) : AnnotationReturnType;

    /**
     * @param ReflectionProperty $refProperty
     * @return AnnotationProperty
     */
    public function getAnnotationProperty(ReflectionProperty $refProperty) : AnnotationProperty;
}