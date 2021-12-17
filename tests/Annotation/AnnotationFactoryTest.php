<?php
namespace Terrazza\Component\Serializer\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Annotation\IAnnotationFactory;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;

class AnnotationFactoryTest extends TestCase {
    /**
     * @param bool $log
     * @return IAnnotationFactory
     */
    private function get(bool $log=false) : IAnnotationFactory {
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