<?php
namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArray;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArrayAsBuiltIn;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArrayAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleSimple;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeInt;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeString;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicBuiltIn;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleVariadicViaParam;

class JsonArrayDeserializerTest extends TestCase {

    function testSimple() {
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
            $object->dInt,

            $objectUpdate->number,
            $objectUpdate->float,
        ]);
    }

    function testArray() {
        $input                                      = json_encode(
            [
                'array' => $array = [1, 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer(true);
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

    function testArrayAsBuiltIn() {
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

    function testArrayAsClass() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleArrayAsClass::class, $input);
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

    function testVariadicAsBuiltIn() {
        $input                                      = json_encode(
      [
                'int' => [$i1 = 1,$i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleVariadicBuiltIn::class, $input);
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

    function testVariadicAsClass() {
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

    function testVariadicViaParam() {
        $input                                      = json_encode(
            [
                'int' => [$i1 = 1,$i2 = 2]
            ]
        );
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(SerializerExampleVariadicViaParam::class, $input);
        $objectUpdate                               = $deserializer->deserialize($object, json_encode(
            [
                'int' => [$i3 = 3]
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

    function testBuiltInSpecials() {
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectDate                                 = $deserializer->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => $date = "31.01.2021"]));
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }

    function testValidTypeSwitch() {
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
}