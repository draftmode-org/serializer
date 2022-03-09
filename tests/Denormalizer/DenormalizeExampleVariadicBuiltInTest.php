<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicBuiltIn;

class DenormalizeExampleVariadicBuiltInTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testCreate() {
        $input                                      = [
            'int' => [$i1 = 1,$i2 = 2]
        ];
        $deserializer                               = Denormalizer::get();
        $object                                     = $deserializer->denormalizeClass(SerializerExampleVariadicBuiltIn::class, $input);
        /*$deserializer                               = Denormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 3]
        ]);*/
        $this->assertEquals([
            [$i1,$i2],
            //[$i3],
        ],[
            $object->int,
            //$objectUpdate->int,
        ]);
    }
}