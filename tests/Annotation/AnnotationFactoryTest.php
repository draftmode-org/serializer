<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUUID;

class AnnotationFactoryTest extends TestCase {
    /** @var int|null */
    private ?int $iValue;
    /** @var array */
    private array $aValue;
    /** @var SerializerRealLifeUUID */
    private $cValue;
    private SerializerRealLifeUUID $cValueIn;
    /** @var int|string */
    private $mBuiltIn;
    /** @var ClassNameResolver|AnnotationFactory */
    private $mTypes;

    private function get() : AnnotationFactoryInterface {
        return new AnnotationFactory(
            new ClassNameResolver()
        );
    }
    function testWithBuiltInAndIsBuiltInType() {
        $factory = $this->get()->withBuiltInTypes(["x"]);
        $this->assertTrue($factory->isBuiltInType("x"));
    }

    function testGetAnnotationProperty() {
        $ref            = new ReflectionClass($this);
        $iProperty      = $this->get()->getAnnotationProperty($ref->getProperty("iValue"));
        $aProperty      = $this->get()->getAnnotationProperty($ref->getProperty("aValue"));
        $cProperty      = $this->get()->getAnnotationProperty($ref->getProperty("cValue"));
        $cInProperty    = $this->get()->getAnnotationProperty($ref->getProperty("cValueIn"));
        $this->assertEquals([
            true,
            true,

            true,
            false,

            false,
            false,
            false,
            SerializerRealLifeUUID::class,

            SerializerRealLifeUUID::class,
        ],[
            $iProperty->isBuiltIn(),
            $iProperty->isOptional(),

            $aProperty->isArray(),
            $aProperty->isOptional(),

            $cProperty->isBuiltIn(),
            $cProperty->isArray(),
            $cProperty->isOptional(),
            $cProperty->getType(),

            $cInProperty->getType(),
        ]);
    }

    function testGetAnnotationPropertyFailureMultipleBuiltIn() {
        $ref            = new ReflectionClass($this);
        $this->expectException(InvalidArgumentException::class);
        $this->get()->getAnnotationProperty($ref->getProperty("mBuiltIn"));
    }

    function testGetAnnotationPropertyFailureMultipleTypes() {
        $ref            = new ReflectionClass($this);
        $this->expectException(InvalidArgumentException::class);
        $this->get()->getAnnotationProperty($ref->getProperty("mTypes"));
    }
}