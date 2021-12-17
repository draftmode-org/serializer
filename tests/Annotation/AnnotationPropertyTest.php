<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\IAnnotationFactory;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeUUID;

class AnnotationPropertyTest extends TestCase {
    /**
     * @param bool $log
     * @return IAnnotationFactory
     */
    private function get(bool $log=false) : IAnnotationFactory {
        return new AnnotationFactory(
            LoggerUnit::getLogger("AnnotationProperty", $log),
            new ClassNameResolver()
        );
    }

    private ?int $intTypeOptional;
    /** @var int|null */
    private $intOptional;

    private array $arrayTypeRequired;
    /** @var array */
    private $arrayRequired;

    private SerializerRealLifeUUID $classTypeRequired;
    /** @var SerializerRealLifeUUID */
    private $classRequired;

    /** @var int[]  */
    private array $arrayAsBuiltIn;
    /** @var int[]|null  */
    private ?array $arrayAsBuiltInOptional;

    /** @var SerializerRealLifeUUID[]  */
    private array $arrayTypeAsClass;

    /** @var SerializerRealLifeUUID[] */
    private $arrayAsClass;

    function testAnnotationProperty() {
        $ref            = new ReflectionClass($this);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "intTypeOptional"));
        $this->assertEquals([
            false,
            true,
            true,
            "int"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "intOptional"));
        $this->assertEquals([
            false,
            true,
            true,
            "int"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);

        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayTypeRequired"));
        $this->assertEquals([
            true,
            true,
            false,
            "array"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayRequired"));
        $this->assertEquals([
            true,
            true,
            false,
            "array"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);

        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "classTypeRequired"));
        $this->assertEquals([
            false,
            false,
            false,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "classRequired"));
        $this->assertEquals([
            false,
            false,
            false,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);

        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayAsBuiltIn"));
        $this->assertEquals([
            true,
            true,
            false,
            "int"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayAsBuiltInOptional"));
        $this->assertEquals([
            true,
            true,
            true,
            "int"
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);


        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayAsClass"));
        $this->assertEquals([
            true,
            false,
            false,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
        $property       = $this->get()->getAnnotationProperty($ref->getProperty($name = "arrayTypeAsClass"));
        $this->assertEquals([
            true,
            false,
            false,
            SerializerRealLifeUUID::class
        ], [
            $property->isArray(),
            $property->isBuiltIn(),
            $property->isOptional(),
            $property->getType(),
        ], $name);
    }
}