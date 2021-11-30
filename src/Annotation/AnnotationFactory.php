<?php
namespace Terrazza\Component\Serializer\Annotation;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Terrazza\Component\ReflectionClass\ClassNameResolverInterface;

class AnnotationFactory implements AnnotationFactoryInterface {
    private ClassNameResolverInterface $classNameResolver;
    private array $builtInTypes;
    CONST BUILT_IN_TYPES                            = ["int", "integer", "float", "double", "string", "DateTime", "NULL"];

    public function __construct(ClassNameResolverInterface $classNameResolver, ?array $builtInTypes=null) {
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
        $returnType                          = new AnnotationReturnType($method->getName());
        $returnType->setDeclaringClass($method->getDeclaringClass()->getName());
        if ($returnTypeType = $method->getReturnType()) {
            $returnType->setBuiltIn($returnTypeType->isBuiltin());
            $returnType->setOptional($returnTypeType->allowsNull());
        }
        $this->extendAnnotationReturnType($method, $returnType);
        return $returnType;
    }

    /**
     * @param ReflectionMethod $method
     * @param AnnotationReturnType $returnType
     */
    private function extendAnnotationReturnType(ReflectionMethod $method, AnnotationReturnType $returnType) : void {
        if (preg_match('/@return\s+([^\s]+)/', $method->getDocComment(), $matches)) {
            $annotation 						= $matches[1];
            $returnType->setBuiltIn($this->isBuiltInByAnnotation($annotation));
            if ($this->isArrayByAnnotation($annotation)) {
                $returnType->setArray(true);
            }
            if (strpos($annotation, "|null")!==false) {
                $returnType->setOptional(true);
            }
            $returnType->setType(
                $this->getTypeByAnnotation($annotation, $returnType->getDeclaringClass())
            );
        }
    }

    /**
     * @param ReflectionMethod $refMethod
     * @param ReflectionParameter $refParameter
     * @return AnnotationParameter
     */
    public function getAnnotationParameter(ReflectionMethod $refMethod, ReflectionParameter $refParameter) : AnnotationParameter {
        $parameter                                  = new AnnotationParameter($refParameter->getName());
        $parameter->setArray($refParameter->isArray());
        $parameter->setVariadic($refParameter->isVariadic());
        $parameter->setOptional($refParameter->isOptional());
        $parameter->setDeclaringClass($refParameter->getDeclaringClass() ? $refParameter->getDeclaringClass()->getName() : null);

        if ($refParameter->isDefaultValueAvailable()) {
            $parameter->setDefaultValueAvailable(true);
            $parameter->setDefaultValue($refParameter->getDefaultValue());
        }
        if ($parameterType = $refParameter->getType()) {
            $parameter->setType($parameterType->getName());
            if ($parameterType->isBuiltIn()) {
                $parameter->setBuiltIn(true);
            }
        }
        $this->extendAnnotationParameter($refMethod, $parameter);
        return $parameter;
    }

    private function extendAnnotationParameter(ReflectionMethod $method, AnnotationParameter $parameter) : void {
        if (preg_match('/@param\\s+(\\S+)\\s+\\$' . $parameter->getName() . '/', $method->getDocComment(), $matches)) {
            $annotation 						= $matches[1];
            $parameter->setBuiltIn($this->isBuiltInByAnnotation($annotation));
            if ($this->isArrayByAnnotation($annotation)) {
                $parameter->setArray(true);
            }
            if (strpos($annotation, "|null")!==false) {
                $parameter->setOptional(true);
            }
            $parameter->setType(
                $this->getTypeByAnnotation($annotation, $parameter->getDeclaringClass())
            );
        }
    }

    /**
     * @param ReflectionProperty $refProperty
     * @return AnnotationProperty
     */
    public function getAnnotationProperty(ReflectionProperty $refProperty) : AnnotationProperty {
        $property                               = new AnnotationProperty($refProperty->getName());
        $property->setDeclaringClass($refProperty->getDeclaringClass() ? $refProperty->getDeclaringClass()->getName() : null);
        if ($refPropertyType = $refProperty->getType()) {
            if ($refPropertyType->isBuiltIn()) {
                $property->setBuiltIn(true);
            } else {
                $property->setType($refPropertyType->getName());
            }
            if ($refPropertyType->allowsNull()) {
                $property->setOptional(true);
            }
        }
        $this->extendAnnotationProperty($refProperty, $property);
        return $property;
    }

    /**
     * @param ReflectionProperty $refProperty
     * @param AnnotationProperty $property
     */
    private function extendAnnotationProperty(ReflectionProperty $refProperty, AnnotationProperty $property) : void {
        if (preg_match('/@var\s+([^\s]+)/', $refProperty->getDocComment(), $matches)) {
            $annotation                         = $matches[1];
            if ($this->isBuiltInByAnnotation($annotation)) {
                $property->setBuiltIn(true);
            }
            if ($this->isArrayByAnnotation($annotation)) {
                $property->setArray(true);
            }
            if (strpos($annotation, "|null")!==false) {
                $property->setOptional(true);
            }
            if (!$property->getType()) {
                if ($annotationType = $this->getTypeByAnnotation($annotation, $property->getDeclaringClass())) {
                    $property->setType($annotationType);
                }
            }
        }
    }

    /**
     * @param string $annotation
     * @param string|null $declaringClass
     * @return string|null
     */
    private function getTypeByAnnotation(string $annotation, ?string $declaringClass) :?string {
        $annotation                             	= strtr($annotation, [
            "[]" => "",
        ]);
        $types                        				= explode("|", $annotation);
        if ($this->isBuiltInByAnnotation($annotation)) {
            $types                        			= array_diff($types, ['null']);
            if (count($types) > 1) {
                throw new InvalidArgumentException("unable to return a unique type from annotation, given $annotation");
            }
            return array_shift($types);
        }
        else {
            $types                        			= array_diff($types, ['array', 'null']);
            if (count($types) > 1) {
                throw new InvalidArgumentException("unable to return a unique type from annotation, given $annotation");
            }
            $type                                   = array_shift($types);
            if (!$type && $this->isArrayByAnnotation($annotation)) {
                return null;
            }
            if ($declaringClass) {
                if ($typeClassName = $this->classNameResolver->getClassName($declaringClass, $type)) {
                    return $typeClassName;
                }
            }
            throw new InvalidArgumentException("unable to resolve annotation type, given $annotation");
        }
    }

    private function isArrayByAnnotation(string $annotation) : bool {
        $types                        		    = explode("|", $annotation);
        foreach ($types as $type) {
            if (in_array($type, ["array"]) || strpos($annotation, "[]")!==false) {
                return true;
            }
        }
        return false;
    }

    private function isBuiltInByAnnotation(string $annotation) : bool {
        $annotation                                 = strtr($annotation, [
            "[]" => ""
        ]);
        $types                        				= explode("|", $annotation);
        foreach ($types as $type) {
            if ($this->isBuiltInType($type)) {
                return true;
            }
        }
        return false;
    }
}