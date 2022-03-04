<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\ArrayDenormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleArrayAsClass;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeInt;

class ArrayDenormalizeExampleArrayClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'array' => [$i1 = 1,$i2 = 2]
        ];
        $deserializer                               = ArrayDenormalizer::get();
        $object                                     = $deserializer->denormalize(SerializerExampleArrayAsClass::class, $input);
        $deserializer                               = ArrayDenormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'array' => [$i3 = 3]
        ]);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            [new SerializerExampleTypeInt($i3)]
        ],[
            $object->array,
            $objectUpdate->array,
        ]);
    }
}