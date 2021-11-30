<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Annotation\AnnotationParameter;

class AnnotationParameterTest extends TestCase {

    function testEmpty() {
        $object = new AnnotationParameter($name="name");
        $this->assertEquals([
            $name,
            $name,
            false,
            false,
            false,
            false,
            null,
            false,
            null,
            null,
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isVariadic(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->isDefaultValueAvailable(),
            $object->getDefaultValue(),
            $object->getType(),
        ]);
    }

    function testSetter() {
        $object = new AnnotationParameter($name="name");
        $object->setArray(true);
        $object->setBuiltIn(true);
        $object->setVariadic(true);
        $object->setOptional(true);
        $object->setDeclaringClass("null");
        $object->setDefaultValueAvailable(true);
        $object->setDefaultValue("null");
        $object->setType("null");
        $this->assertEquals([
            $name,
            $name,
            true,
            true,
            true,
            true,
            "null",
            true,
            "null",
            "null",
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isVariadic(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->isDefaultValueAvailable(),
            $object->getDefaultValue(),
            $object->getType(),
        ]);
    }
}