<?php
namespace Terrazza\Component\Serializer\Denormalizer;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;
use Terrazza\Component\ReflectionClass\ClassNameResolverInterface;

class AnnotationFactory implements AnnotationFactoryInterface {
    private ClassNameResolverInterface $classNameResolver;
    private array $builtInTypes=[];
    public function __construct(ClassNameResolverInterface $classNameResolver) {
        $this->classNameResolver                    = $classNameResolver;
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

    private function getTypeByAnnotation(string $annotation, ?string $declaringClass) : string {
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
            if ($declaringClass) {
                if ($typeClassName = $this->classNameResolver->getClassName($declaringClass, $type)) {
                    return $typeClassName;
                }
            }
            throw new InvalidArgumentException("unable to resolve an annotation type, given $annotation");
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
            if (in_array($type, $this->builtInTypes)) {
                return true;
            }
        }
        return false;
    }
}