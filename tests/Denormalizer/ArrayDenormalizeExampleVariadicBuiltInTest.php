<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\DenormalizerMock;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicBuiltIn;

class ArrayDenormalizeExampleVariadicBuiltInTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'int' => [$i1 = 1,$i2 = 2]
        ];
        $deserializer                               = DenormalizerMock::get();
        $object                                     = $deserializer->denormalize(SerializerExampleVariadicBuiltIn::class, $input);
        $deserializer                               = DenormalizerMock::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 3]
        ]);
        $this->assertEquals([
            [$i1,$i2],
            [$i3],
        ],[
            $object->int,
            $objectUpdate->int,
        ]);
    }
}