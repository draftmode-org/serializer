<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Annotation\AnnotationProperty;

class AnnotationPropertyTest extends TestCase {

    function testEmpty() {
        $object = new AnnotationProperty($name="name");
        $this->assertEquals([
            $name,
            $name,
            false,
            false,
            false,
            null,
            null,
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->getType(),
        ]);
    }

    function testSetter() {
        $object = new AnnotationProperty($name="name");
        $object->setArray(true);
        $object->setBuiltIn(true);
        $object->setOptional(true);
        $object->setDeclaringClass("null");
        $object->setType("null");
        $this->assertEquals([
            $name,
            $name,
            true,
            true,
            true,
            "null",
            "null",
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->getType(),
        ]);
    }
}