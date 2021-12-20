<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\DenormalizerMock;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleArray;

class ArrayDenormalizeExampleArrayTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'array' => $array = [1, 2]
        ];
        $deserializer                               = DenormalizerMock::get();
        $object                                     = $deserializer->denormalize(SerializerExampleArray::class, $input);
        $deserializer                               = DenormalizerMock::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'array' => $arrayUpdate = [1, 2, 3]
        ]);
        $this->assertEquals([
            $array,
            $arrayUpdate
        ],[
            $object->array,
            $objectUpdate->array
        ]);
    }
}