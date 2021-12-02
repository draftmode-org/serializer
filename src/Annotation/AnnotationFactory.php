<?php
namespace Terrazza\Component\Serializer\Annotation;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\ReflectionClass\ClassNameResolverInterface;

class AnnotationFactory implements AnnotationFactoryInterface {
    private LogInterface $logger;
    private ClassNameResolverInterface $classNameResolver;
    private array $builtInTypes;
    CONST BUILT_IN_TYPES                            = ["int", "integer", "float", "double", "string", "array", "NULL"];

    public function __construct(LogInterface $logger, ClassNameResolverInterface $classNameResolver, ?array $builtInTypes=null) {
        $this->logger                               = $logger;
        $this->classNameResolver                    = $classNameResolver;
        $this->builtInTypes                         = $builtInTypes ?? self::BUILT_IN_TYPES;
    }

    /**
     * @param array $builtInTypes
     * @return AnnotationFactoryInterface
     */
    public function withBuiltInTypes(array $builtInTypes): AnnotationFactoryInterface {
        $annotationFactory                          = clone $this;
        $annotationFactory->builtInTypes            = $builtInTypes;
        return $annotationFactory;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isBuiltInType(string $type): bool {
        return in_array($type, $this->builtInTypes);
    }

    /**
     * @param ReflectionMethod $method
     * @return AnnotationReturnType
     */
    public function getAnnotationReturnType(ReflectionMethod $method): AnnotationReturnType {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $returnType                                 = new AnnotationReturnType($method->getName());
        $returnType->setDeclaringClass($method->getDeclaringClass()->getName());

        if ($method->hasReturnType()) {
            /** @var ReflectionNamedType $type */
            $type                                   = $method->getReturnType();
            $logger->debug("ReflectionMethod ".$method->getName()." as Type",
                ["line" => __LINE__, "type" => $type->getName(), "isBuiltIn" => $type->isBuiltIn(), "isOptional" => $type->allowsNull()]);
            $returnType->setBuiltIn($type->isBuiltin());
            $returnType->setOptional($type->allowsNull());
            $returnType->setType($type->getName());
            if ($returnType->getType()==="array") {
                $returnType->setArray(true);
                $returnType->setBuiltIn(true);
            }
        }
        if ($annotation = $this->getReturnTypeAnnotation($method)) {
            $logger->debug("ReflectionMethod ".$method->getName()." hasAnnotation $annotation");
            $this->extendTypeWithAnnotation($returnType, $annotation);
        }
        return $returnType;
    }

    /**
     * @param ReflectionProperty $refProperty
     * @return AnnotationProperty
     */
    public function getAnnotationProperty(ReflectionProperty $refProperty): AnnotationProperty {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $property                                   = new AnnotationProperty($refProperty->getName());
        $property->setDeclaringClass($refProperty->getDeclaringClass()->getName());
        if ($refProperty->hasType()) {
            /** @var ReflectionNamedType $type */
            $type                                   = $refProperty->getType();
            $logger->debug("ReflectionProperty ".$refProperty->getName()." has Type",
                ["line" => __LINE__, "type" => $type->getName(), "isBuiltIn" => $type->isBuiltIn(), "isOptional" => $type->allowsNull()]);
            $property->setBuiltIn($type->isBuiltin());
            $property->setOptional($type->allowsNull());
            $property->setType($type->getName());
            if ($property->getType()==="array") {
                $property->setArray(true);
                $property->setBuiltIn(true);
            }
        }
        if ($annotation = $this->getPropertyVarAnnotation($refProperty)) {
            $logger->debug("ReflectionProperty ".$refProperty->getName()." hasAnnotation $annotation");
            $this->extendTypeWithAnnotation($property, $annotation);
        }
        return $property;
    }

    /**
     * @param ReflectionMethod $refMethod
     * @param ReflectionParameter $refParameter
     * @return AnnotationParameter
     */
    public function getAnnotationParameter(ReflectionMethod $refMethod, ReflectionParameter $refParameter) : AnnotationParameter {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        $parameter                                  = new AnnotationParameter($refParameter->getName());
        $parameter->setArray($refParameter->isArray());
        $parameter->setOptional($refParameter->isOptional());
        $parameter->setDeclaringClass($refParameter->getDeclaringClass() ? $refParameter->getDeclaringClass()->getName() : null);
        if ($refParameter->isVariadic()) {
            $parameter->setArray(true);
            $parameter->setVariadic(true);
            $parameter->setOptional(true);
        }
        if ($refParameter->isDefaultValueAvailable()) {
            $parameter->setDefaultValueAvailable($refParameter->isDefaultValueAvailable());
            $parameter->setDefaultValue($refParameter->getDefaultValue());
        }
        if ($refParameter->hasType()) {
            /** @var ReflectionNamedType $type */
            $type                                   = $refParameter->getType();
            $logger->debug("ReflectionParameter ".$refParameter->getName()." has Type",
                ["line" => __LINE__, "type" => $type->getName(), "isBuiltIn" => $type->isBuiltIn(), "isOptional" => $type->allowsNull()]);
            $parameter->setBuiltIn($type->isBuiltin());
            $parameter->setOptional($type->allowsNull());
            $parameter->setType($type->getName());
            if ($parameter->getType()==="array") {
                $parameter->setArray(true);
                $parameter->setBuiltIn(true);
            }
        }
        if ($annotation = $this->getParameterAnnotation($refMethod, $refParameter)) {
            $logger->debug("ReflectionParameter ".$refParameter->getName()." hasAnnotation $annotation");
            $this->extendTypeWithAnnotation($parameter, $annotation);
        } else {
            //$logger->debug("ReflectionParameter ".$refParameter->getName()." has no annotation");
        }
        return $parameter;
    }

    /**
     * @param AnnotationTypeInterface $annotationType
     * @param string $annotation
     */
    private function extendTypeWithAnnotation(AnnotationTypeInterface $annotationType, string $annotation) : void {
        if ($type = $this->getBuildInByAnnotation($annotation)) {
            $annotationType->setBuiltIn(true);
            $annotationType->setType($type);
        }
        if ($this->isArrayByAnnotation($annotation)) {
            $annotationType->setArray(true);
            $annotationType->setBuiltIn(true);
        }
        if ($this->isOptionalByAnnotation($annotation)) {
            $annotationType->setOptional(true);
        }
        if ($typeClass = $this->getTypeClassByAnnotation($annotation, $annotationType->getDeclaringClass())) {
            $annotationType->setType($typeClass);
            $annotationType->setBuiltIn(false);
        }
    }

    /**
     * @param string $annotation
     * @param string|null $declaringClass
     * @return string|null
     */
    private function getTypeClassByAnnotation(string $annotation, ?string $declaringClass) :?string {
        $logger                                     = $this->logger->withMethod(__METHOD__);
        if ($this->isBuiltInByAnnotation($annotation)) {
            $logger->debug("annotation $annotation isBuiltInAnnotation");
            return null;
        }
        $annotation                             	= strtr($annotation, [
            "[]" => "",
        ]);
        $types                        				= explode("|", $annotation);
        $types                        			    = array_diff($types, ['array', 'null']);
        $type                                       = array_shift($types);
        if ($type && $declaringClass) {
            if ($typeClassName = $this->classNameResolver->getClassName($declaringClass, $type)) {
                return $typeClassName;
            }
        }
        return null;
    }

    /**
     * @param ReflectionMethod $method
     * @return string|null
     */
    private function getReturnTypeAnnotation(ReflectionMethod $method) :?string {
        if (preg_match('/@return\s+([^\s]+)/', $method->getDocComment(), $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param ReflectionParameter $parameter
     * @return string|null
     */
    private function getParameterAnnotation(ReflectionMethod $method, ReflectionParameter $parameter) :?string {
        if (preg_match('/@param\\s+(\\S+)\\s+\\$' . $parameter->getName() . '/', $method->getDocComment(), $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    /**
     * @param ReflectionProperty $property
     * @return string|null
     */
    private function getPropertyVarAnnotation(ReflectionProperty $property) :?string {
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }

    /**
     * @param string $annotation
     * @return bool
     */
    private function isOptionalByAnnotation(string $annotation) : bool {
        return (strpos($annotation, "|null")!==false);
    }

    /**
     * @param string $annotation
     * @return bool
     */
    private function isArrayByAnnotation(string $annotation) : bool {
        $types                        		    = explode("|", $annotation);
        foreach ($types as $type) {
            if (in_array($type, ["array"]) || strpos($annotation, "[]")!==false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $annotation
     * @return string|null
     */
    private function getBuildInByAnnotation(string $annotation) :?string {
        $annotation                                 = strtr($annotation, [
            "[]" => ""
        ]);
        $types                        				= explode("|", $annotation);
        foreach ($types as $type) {
            if ($this->isBuiltInType($type)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @param string $annotation
     * @return bool
     */
    private function isBuiltInByAnnotation(string $annotation) : bool {
        $logger                                      = $this->logger->withMethod(__METHOD__);
        $annotation                                 = strtr($annotation, [
            "[]" => ""
        ]);
        $types                        				= explode("|", $annotation);
        $isBuiltIn                                  = null;
        foreach ($types as $type) {
            $typeIsBuiltIn                          = $this->isBuiltInType($type);
            if ($typeIsBuiltIn) {
                $logger->debug("type $type isBuiltIn", ["line" => __LINE__]);
            } else {
                $logger->debug("type $type is not BuiltIn", ["line" => __LINE__]);
            }
            $isBuiltIn                              = ($isBuiltIn === null) ? $typeIsBuiltIn : ($isBuiltIn && $typeIsBuiltIn);
            if (!$isBuiltIn) {
                return false;
            }
        }
        return $isBuiltIn;
    }
}