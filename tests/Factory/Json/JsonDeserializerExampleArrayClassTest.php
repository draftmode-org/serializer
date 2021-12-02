<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArrayAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeInt;

class JsonDeserializerExampleArrayClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleArrayAsClass::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'array' => [$i3 = 3]
            ]
        ));
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            [new SerializerExampleTypeInt($i3)]
        ],[
            $object->array,
            $objectUpdate->array,
        ]);
    }
}