<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleSimple;

class JsonDeserializerExampleSimpleTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = json_encode(
            [
                'number' => $number = 1,
                'float' => $float = 1.2,
                'string' => $string = "string",
                'array' => $array = [1,2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleSimple::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode([
            "number" => $numberUpdate = 3,
            "float" => $floatUpdate = 3.1,
        ]));
        $this->assertEquals([
            $number,
            $float,
            $string,
            $array,
            2,

            $numberUpdate,
            $floatUpdate,
        ],[
            $object->number,
            $object->float,
            $object->string,
            $object->array,
            $object->dInt,                          // has default value

            $objectUpdate->number,
            $objectUpdate->float,
        ]);
    }
}