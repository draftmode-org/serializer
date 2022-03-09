<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleArray;

class DenormalizeExampleArrayTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testCreate() {
        $input                                      = [
            'array' => $array = [1, 2]
        ];
        $deserializer                               = Denormalizer::get();
        $object                                     = $deserializer->denormalizeClass(SerializerExampleArray::class, $input);
        /*$deserializer                               = Denormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'array' => $arrayUpdate = [1, 2, 3]
        ]);*/
        $this->assertEquals([
            $array,
            //$arrayUpdate
        ],[
            $object->array,
            //$objectUpdate->array
        ]);
    }
}