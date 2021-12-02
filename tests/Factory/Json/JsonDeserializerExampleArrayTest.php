<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArray;

class JsonDeserializerExampleArrayTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = json_encode(
            [
                'array' => $array = [1, 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleArray::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'array' => $arrayUpdate = [1, 2, 3]
            ]
        ));
        $this->assertEquals([
            $array,
            $arrayUpdate
        ],[
            $object->array,
            $objectUpdate->array
        ]);
    }
}