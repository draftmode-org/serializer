<?php

namespace Terrazza\Component\Serializer\Tests\Annotation;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Annotation\AnnotationTypeTrait;

class AnnotationTypeTraitTest extends TestCase {

    function testClassEmpty() {
        $object = new AnnotationTypeTraitTestClass($name="name");
        $this->assertEquals([
            $name,
            $name,
            false,
            false,
            false,
            null,
            false,
            null,
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->hasDeclaringClass(),
            $object->getType(),
        ]);
    }

    function testClassSetter() {
        $object = new AnnotationTypeTraitTestClass($name="name");
        $object->setArray(true);
        $object->setBuiltIn(true);
        $object->setOptional(true);
        $object->setDeclaringClass($declaringClass = "declaringClass");
        $object->setType($type = "type");
        $this->assertEquals([
            $name,
            $name,
            true,
            true,
            true,
            $declaringClass,
            true,
            $type,
        ],[
            $object->getName(),
            $object,
            $object->isArray(),
            $object->isBuiltIn(),
            $object->isOptional(),
            $object->getDeclaringClass(),
            $object->hasDeclaringClass(),
            $object->getType(),
        ]);
    }
}

class AnnotationTypeTraitTestClass {
    use AnnotationTypeTrait;
}