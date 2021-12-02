<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArrayAsBuiltIn;

class JsonDeserializerExampleArrayBuiltInTest extends TestCase {

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
        $object                                     = $deserializer->deserialize(SerializerExampleArrayAsBuiltIn::class, $input);
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'array' => [$i3 = 3]
            ]
        ));
        $this->assertEquals([
            [$i1, $i2],
            [$i3],
        ],[
            $object->array,
            $objectUpdate->array
        ]);
    }
}