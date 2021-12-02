<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeInt;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicViaParam;

class JsonDeserializerExampleVariadicClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = json_encode(
            [
                'int' => [$i1 = 1, $i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleVariadicAsClass::class, $input);
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'int' => [$i3 = 3]
            ]
        ));
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
        $input                                      = json_encode(
            [
                'int' => [$i1 = 2,$i2 = 3]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleVariadicViaParam::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'int' => [$i3 = 4]
            ]
        ));
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            [new SerializerExampleTypeInt($i3)]
        ],[
            $object->int,
            $objectUpdate->int,
        ]);
    }
}