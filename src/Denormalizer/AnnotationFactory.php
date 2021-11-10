<?php
namespace Terrazza\Component\Serializer\Denormalizer;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Terrazza\Component\ReflectionClass\ClassNameInterface;

class AnnotationFactory implements AnnotationFactoryInterface {
    private ClassNameInterface $reflectionClassName;
    public function __construct(ClassNameInterface $reflectionClassName) {
        $this->reflectionClassName                  = $reflectionClassName;
    }

    /**
     * @param string $annotation
     * @return string|null
     */
    public function extractTypeFromAnnotation(string $annotation) :?string {
        $annotation                                 = strtr($annotation, [
            "[]" => ""
        ]);
        $annotationTypes                            = explode("|", $annotation);
        $annotationTypes                            = array_diff($annotationTypes, ['array']);
        if (count($annotationTypes) > 1) {
            throw new InvalidArgumentException("unable to return a unique type, multiple types given");
        }
        return array_shift($annotationTypes);
    }

    /**
     * @param string $parentClass
     * @param string $findClass
     * @return string|null
     */
    public function getClassName(string $parentClass, string $findClass) :?string {
        return $this->reflectionClassName->getClassName($parentClass, $findClass);
    }

    /**
     * @param ReflectionProperty $property
     * @return string|null
     */
    public function getPropertyTypeByAnnotation(ReflectionProperty $property) :?string {
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            return $this->extractTypeFromAnnotation($matches[1]);
        }
        return null;
    }

    /**
     * @param ReflectionMethod $method
     * @param string $parameterName
     * @return string|null
     */
    public function getParameterTypeByAnnotation(ReflectionMethod $method, string $parameterName) :?string {
        if (preg_match('/@param\\s+(\\S+)\\s+\\$' . $parameterName . '/', $method->getDocComment(), $matches)) {
            return $this->extractTypeFromAnnotation($matches[1]);
        }
        return null;
    }
}