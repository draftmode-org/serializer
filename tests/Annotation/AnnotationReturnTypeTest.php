<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Annotation\AnnotationReturnType;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;

class AnnotationReturnTypeTest extends TestCase {
    /**
     * @param bool $log
     * @return AnnotationFactoryInterface
     */
    private function get(bool $log=false) : AnnotationFactoryInterface {
        return new AnnotationFactory(
            LoggerUnit::getLogger("AnnotationReturnType", $log),
            new ClassNameResolver()
        );
    }

    protected function returnTypeBuiltIn() : int { return 1;}
    protected function returnTypeBuiltInOptional() : ?int { return 1;}
    protected function returnTypeArray() : array { return [];}
    protected function returnTypeArrayOptional() : ?array { return [];}

    /**
     * @return int
     */
    protected function returnBuiltIn() { return 1;}

    /**
     * @return int|null
     */
    protected function returnBuiltInOptional() { return 1;}

    /**
     * @return array
     */
    protected function returnArray() { return [];}

    /**
     * @return array|null
     */
    protected function returnArrayOptional() { return [];}

    /**
     * @return int[]
     */
    protected function returnArrayAsBuiltIn() : array {return [1];}

    /**
     * @return int[]|null
     */
    protected function returnArrayAsBuiltInOptional() {return [1];}

    /**
     * @return AnnotationReturnType[]
     */
    protected function returnTypeArrayAsClass() : array {return [new AnnotationReturnType("name")];}

    /**
     * @return mixed|null
     */
    protected function returnMixedOptional() { return null;}

    function testAnnotationReturnType() {
        $ref            = new ReflectionClass($this);

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnTypeBuiltIn"));
        $this->assertEquals([
            false,
            true,
            false,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeBuiltIn");
        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnBuiltIn"));
        $this->assertEquals([
            false,
            true,
            false,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeBuiltIn");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnTypeBuiltInOptional"));
        $this->assertEquals([
            false,
            true,
            true,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeBuiltInOptional");
        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnBuiltInOptional"));
        $this->assertEquals([
            false,
            true,
            true,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnBuiltInOptional");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnTypeArray"));
        $this->assertEquals([
            true,
            true,
            false,
            "array"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeArray");
        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnArray"));
        $this->assertEquals([
            true,
            true,
            false,
            "array"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnArray");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnTypeArrayOptional"));
        $this->assertEquals([
            true,
            true,
            true,
            "array"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeArrayOptional");
        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnArrayOptional"));
        $this->assertEquals([
            true,
            true,
            true,
            "array"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnArrayOptional");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnArrayAsBuiltIn"));
        $this->assertEquals([
            true,
            true,
            false,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnArrayAsBuiltIn");
        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnArrayAsBuiltInOptional"));
        $this->assertEquals([
            true,
            true,
            true,
            "int"
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnArrayAsBuiltInOptional");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnTypeArrayAsClass"));
        $this->assertEquals([
            true,
            false,
            false,
            AnnotationReturnType::class
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnTypeArrayAsClass");

        $returnType     = $this->get()->getAnnotationReturnType($ref->getMethod("returnMixedOptional"));
        $this->assertEquals([
            false,
            false,
            true,
            null
        ], [
            $returnType->isArray(),
            $returnType->isBuiltIn(),
            $returnType->isOptional(),
            $returnType->getType(),
        ], "returnMixedOptional");
    }

}