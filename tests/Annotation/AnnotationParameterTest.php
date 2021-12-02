<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationParameter;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeUUID;

class AnnotationParameterTest extends TestCase {

    /**
     * @param bool $log
     * @return AnnotationFactoryInterface
     */
    private function get(bool $log=false) : AnnotationFactoryInterface {
        return new AnnotationFactory(
            LoggerUnit::getLogger("AnnotationParameter", $log),
            new ClassNameResolver()
        );
    }

    function testClassEmpty() {
        $object = new AnnotationParameter("name");
        $this->assertEquals([
            false,
            null,
        ],[
            $object->isDefaultValueAvailable(),
            $object->getDefaultValue(),
        ]);
    }

    function testClassSetter() {
        $object = new AnnotationParameter("name");
        $object->setDefaultValueAvailable(true);
        $object->setDefaultValue($defaultValue = "defaultValue");
        $this->assertEquals([
            true,
            $defaultValue,
        ],[
            $object->isDefaultValueAvailable(),
            $object->getDefaultValue(),
        ]);
    }

    /**
     * @param string $methodName
     * @return ReflectionMethod|null
     * @throws ReflectionException
     */
    private function getMethod(string $methodName) :?ReflectionMethod {
        $ref                                        = new ReflectionClass($this);
        return $ref->getMethod($methodName);
    }

    /**
     * @param ReflectionMethod $method
     * @param string $parameterName
     * @return ReflectionParameter|null
     */
    private function getParameter(ReflectionMethod $method, string $parameterName) :?ReflectionParameter {
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->getName() === $parameterName) {
                return $parameter;
            }
        }
        return null;
    }

    /**
     * @param string $date (yes)
     */
    function simpleParamWithContent($date) : void {}
    function testAnnotationParameterWithContent() {
        $method         = $this->getMethod($methodName = "simpleParamWithContent");
        $parameter      = $this->getParameter($method, $parameterName="date");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            false,
            false,
            true,
            false,
            false,
            null,
            "string"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);
    }

    /**
     * @param array $arrayRequired
     * @param int[] $arrayInt
     * @param SerializerRealLifeUUID[] $arrayClass
     * @param SerializerRealLifeUUID|null $classOptional
     * @param int|null $intDefault
     * @param int[] $variadicInt
     */
    function simpleType(int $intRequired, array $arrayRequired, array $arrayInt, array $arrayClass, ?SerializerRealLifeUUID $classOptional, int $intDefault=12, ...$variadicInt) : void {}

    /**
     * @throws ReflectionException
     */
    function testAnnotationParameterSimple() {
        $method         = $this->getMethod($methodName = "simpleType");
        $parameter      = $this->getParameter($method, $parameterName="intRequired");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            false,
            false,
            true,
            false,
            false,
            null,
            "int"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="intDefault");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            false,
            false,
            true,
            true,
            true,
            12,
            "int"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="arrayRequired");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            true,
            false,
            true,
            false,
            false,
            null,
            "array"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="arrayInt");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            true,
            false,
            true,
            false,
            false,
            null,
            "int"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="arrayClass");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            true,
            false,
            false,
            false,
            false,
            null,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="classOptional");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            false,
            false,
            false,
            true,
            false,
            null,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="variadicInt");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            true,
            true,
            true,
            true,
            false,
            null,
            "int"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);
    }

    /**
     * @param int $intRequired
     * @param SerializerRealLifeUUID[] $variadicClass
     */
    function simpleVariadic($intRequired, ...$variadicClass) : void {}

    /**
     * @throws ReflectionException
     */
    function testAnnotationParameterVariadic() {
        $method         = $this->getMethod($methodName = "simpleVariadic");

        $parameter      = $this->getParameter($method, $parameterName="intRequired");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            false,
            false,
            true,
            false,
            false,
            null,
            "int"
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);

        $parameter      = $this->getParameter($method, $parameterName="variadicClass");
        $property       = $this->get()->getAnnotationParameter($method, $parameter);
        $this->assertEquals([
            true,
            true,
            false,
            true,
            false,
            null,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isVariadic(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->isDefaultValueAvailable(),
            $property->getDefaultValue(),
            $property->getType(),
        ], $methodName.":".$parameterName);
    }
}