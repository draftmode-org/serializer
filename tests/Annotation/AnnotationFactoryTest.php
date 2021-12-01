<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\AnnotationFactoryInterface;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;

class AnnotationFactoryTest extends TestCase {
    /**
     * @param bool $log
     * @return AnnotationFactoryInterface
     */
    private function get(bool $log=false) : AnnotationFactoryInterface {
        return new AnnotationFactory(
            LoggerUnit::getLogger("Annotation", $log),
            new ClassNameResolver()
        );
    }

    function testWithBuiltInAndIsBuiltInType() {
        $factory = $this->get()->withBuiltInTypes(["x"]);
        $this->assertTrue($factory->isBuiltInType("x"));
    }
}