<?php
namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Factory\Json\JsonArrayDataSerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleArray;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleArrayAsBuiltIn;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleArrayAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleEmpty;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleSimple;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeInt;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeString;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleVariadicAsClass;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleVariadicBuiltIn;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleVariadicViaParam;

class JsonArrayDataSerializerByPropertyTest extends TestCase {
    private function getSerializer() : SerializerInterface {
        return new JsonArrayDataSerializer();
    }
    function xtestSimple() {
        $input                                      = json_encode(
            [
                    'number' => $number = 1,
                    'float' => $float = 1.2,
                    'string' => $string = "string",
                    'array' => $array = [1,2]
                ]
        );
        $serializer                                 = $this->getSerializer();
        $object                                     = $serializer->deserialize(SerializerExampleSimple::class, $input);
        $objectUpdate                               = $serializer->deserialize($object, json_encode([
            "number" => $numberUpdate = 3
        ]));
        $this->assertEquals([
            $number,
            $float,
            $string,
            $array,
            2,

            $numberUpdate
        ],[
            $object->number,
            $object->float,
            $object->string,
            $object->array,
            $object->dInt,

            $objectUpdate->number
        ]);
    }

    function xtestArray() {
        $input                                      = json_encode(
            [
                'array' => $array = [1, 2]
            ]
        );
        $serializer                                 = $this->getSerializer();
        $object                                     = $serializer->deserialize(SerializerExampleArray::class, $input);
        $objectUpdate                               = $serializer->deserialize($object, json_encode(
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

    function xtestArrayAsBuiltIn() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $serializer                                 = $this->getSerializer();
        $object                                     = $serializer->deserialize(SerializerExampleArrayAsBuiltIn::class, $input);
        $objectUpdate                               = $serializer->deserialize($object, json_encode(
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

    function xtestArrayAsClass() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $serializer                                 = $this->getSerializer();
        $object                                     = $serializer->deserialize(SerializerExampleArrayAsClass::class, $input);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)]
        ],[
            $object->array
        ]);
    }
/*
        function testVariadicAsBuiltIn() {
            $input                                      = json_encode(
          [
                    'int' => [$i1 = 1,$i2 = 2]
                ]
            );
            $serializer                                 = $this->getSerializer();
            $object                                     = $serializer->deserialize(SerializerExampleVariadicBuiltIn::class, $input);
            $this->assertEquals([
                [$i1,$i2]
            ],[
                $object->int
            ]);
        }

        function testVariadicAsClass() {
            $input                                      = json_encode(
                [
                    'int' => [$i1 = 1,$i2 = 2]
                ]
            );
            $serializer                                 = $this->getSerializer();
            $object                                     = $serializer->deserialize(SerializerExampleVariadicAsClass::class, $input);
            $this->assertEquals([
                [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)]
            ],[
                $object->int
            ]);
        }

        function testVariadicViaParam() {
            $input                                      = json_encode(
                [
                    'int' => [$i1 = 1,$i2 = 2]
                ]
            );
            $serializer                                 = $this->getSerializer();
            $object                                     = $serializer->deserialize(SerializerExampleVariadicViaParam::class, $input);
            $this->assertEquals([
                [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)]
            ],[
                $object->int
            ]);
        }

        function testBuiltInSpecials() {
            $serializer                                 = $this->getSerializer();
            $objectDate                                 = $serializer->deserialize(SerializerExampleTypeDateTime::class, json_encode(
                ["date" => $date = "31.01.2021"]));
            $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
        }

        function testValidTypeSwitch() {
            $serializer                                 = $this->getSerializer();
            $objectStringInt                            = $serializer->deserialize(SerializerExampleTypeString::class, json_encode(
                ["string" => $valueInt = 1]));
            $objectStringFloat                          = $serializer->deserialize(SerializerExampleTypeString::class, json_encode(
                ["string" => $valueFloat = 1.1]));
            $objectIntFloat                             = $serializer->deserialize(SerializerExampleTypeFloat::class, json_encode(
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
    */
}