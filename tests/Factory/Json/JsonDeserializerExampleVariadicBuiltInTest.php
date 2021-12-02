<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicBuiltIn;

class JsonDeserializerExampleVariadicBuiltInTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = json_encode(
            [
                'int' => [$i1 = 1,$i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleVariadicBuiltIn::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'int' => [$i3 = 3]
            ]
        ));
        $this->assertEquals([
            [$i1,$i2],
            [$i3],
        ],[
            $object->int,
            $objectUpdate->int,
        ]);
    }
}