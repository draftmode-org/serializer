<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeString;

class JsonDeserializerTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testTypeSwitch() {
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectStringInt                            = $deserializer->deserialize(SerializerExampleTypeString::class, json_encode(
            ["string" => $valueInt = 1]));
        $objectStringFloat                          = $deserializer->deserialize(SerializerExampleTypeString::class, json_encode(
            ["string" => $valueFloat = 1.1]));
        $objectIntFloat                             = $deserializer->deserialize(SerializerExampleTypeFloat::class, json_encode(
            ["float" => $valueInt]));
        $this->assertEquals([
            (string)$valueInt,
            (string)$valueFloat,
            $valueInt
        ], [
            $objectStringInt->string,
            $objectStringFloat->string,
            $objectIntFloat->float,
        ]);
    }

    function testDateTime() {
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectDate                                 = $deserializer->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => $date = "31.01.2021"]));
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }
}