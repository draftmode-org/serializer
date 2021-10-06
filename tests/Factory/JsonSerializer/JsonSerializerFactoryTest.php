<?php
namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Factory\JsonSerializerFactory;
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

class JsonSerializerFactoryTest extends TestCase {

    function testSimple() {
        $input                                      = json_encode(
            [
                    'number' => $number = 1,
                    'float' => $float = 1.2,
                    'string' => $string = "string",
                    'array' => $array = [1,2]
                ]
        );
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleSimple::class, $input);
        $this->assertEquals([
            $number,
            $float,
            $string,
            $array,
            2
        ],[
            $object->number,
            $object->float,
            $object->string,
            $object->array,
            $object->dInt,
        ]);
    }

    function testArray() {
        $input                                      = json_encode(
            [
                'array' => $array = [1, 2]
            ]
        );
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleArray::class, $input);
        $this->assertEquals([
            $array
        ],[
            $object->array
        ]);
    }

    function testArrayAsBuiltIn() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleArrayAsBuiltIn::class, $input);
        $this->assertEquals([
            [$i1, $i2]
        ],[
            $object->array
        ]);
    }

    function testArrayAsClass() {
        $input                                      = json_encode(
            [
                'array' => [$i1 = 1,$i2 = 2]
            ]
        );
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleArrayAsClass::class, $input);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)]
        ],[
            $object->array
        ]);
    }

    function testVariadicAsBuiltIn() {
        $input                                      = json_encode(
      [
                'int' => [$i1 = 1,$i2 = 2]
            ]
        );
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleVariadicBuiltIn::class, $input);
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
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleVariadicAsClass::class, $input);
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
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleVariadicViaParam::class, $input);
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)]
        ],[
            $object->int
        ]);
    }

    function testConstructorWithoutArgs() {
        $input                                      = json_encode([]);
        $factory                                    = new JsonSerializerFactory();
        $object                                     = $factory->deserialize(SerializerExampleEmpty::class, $input);
        $this->assertInstanceOf(SerializerExampleEmpty::class, $object);
    }

    function testBuiltInSpecials() {
        $factory                                    = new JsonSerializerFactory();
        /** @var SerializerExampleTypeDateTime $objectDate */
        $objectDate                                 = $factory->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => $date = "31.01.2021"]));
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }

    function testValidTypeSwitch() {
        $factory                                    = new JsonSerializerFactory();
        $objectStringInt                            = $factory->deserialize(SerializerExampleTypeString::class, json_encode(
            ["string" => $valueInt = 1]));
        $objectStringFloat                          = $factory->deserialize(SerializerExampleTypeString::class, json_encode(
            ["string" => $valueFloat = 1.1]));
        $objectIntFloat                             = $factory->deserialize(SerializerExampleTypeFloat::class, json_encode(
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