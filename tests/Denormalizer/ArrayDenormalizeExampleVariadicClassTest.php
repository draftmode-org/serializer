<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\DenormalizerMock;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeInt;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicAsClass;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicViaParam;

class ArrayDenormalizeExampleVariadicClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'int' => [$i1 = 1, $i2 = 2]
        ];
        $deserializer                               = DenormalizerMock::get();
        $object                                     = $deserializer->denormalize(SerializerExampleVariadicAsClass::class, $input);
        $objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 3]
        ]);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            [new SerializerExampleTypeInt($i3)],
        ],[
            $object->int,
            $objectUpdate->int,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    function testVariadicViaParam() {
        $input                                      = [
            'int' => [$i1 = 2,$i2 = 3]
        ];
        $deserializer                               = DenormalizerMock::get();
        $object                                     = $deserializer->denormalize(SerializerExampleVariadicViaParam::class, $input);
        $deserializer                               = DenormalizerMock::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 4]
        ]);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            [new SerializerExampleTypeInt($i3)]
        ],[
            $object->int,
            $objectUpdate->int,
        ]);
    }
}