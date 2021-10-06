<?php
namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Factory\JsonSerializerFactory;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleSimple;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeInt;

class JsonSerializerFactoryConstructorExceptionTest extends TestCase {

    function testRequiredArgument() {
        $input                                      = json_encode([]);
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(SerializerExampleSimple::class, $input);
    }

    function testUnknownClass() {
        $input                                      = json_encode([]);
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(ReflectionException::class);
        $factory->deserialize("SerializerExampleConstructorSimple::class", $input);
    }

    function testMissingConstructor() {
        $input                                      = json_encode([]);
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(JsonSerializerFactoryConstructorExceptionNoConstructor::class, $input);
    }

    function testMultipleAnnotations() {
        $input                                      = json_encode([
            "int" => 1
        ]);
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(JsonSerializerFactoryConstructorExceptionMultipleAnnotation::class, $input);
    }

    function testInvalidTypeStringInt() {
        $input                                      = json_encode(["number" => "string"]);
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(SerializerExampleTypeInt::class, $input);
    }

    function testInvalidDateTimeDate() {
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => "31.02.2021"]));
    }

    function testInvalidDateTimeValue() {
        $factory                                    = new JsonSerializerFactory();
        $this->expectException(InvalidArgumentException::class);
        $factory->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => "hallo"]));
    }
}

class JsonSerializerFactoryConstructorExceptionNoConstructor {}
class JsonSerializerFactoryConstructorExceptionMultipleAnnotation {
    /**
     * @param int[]|string[] $int
     */
    public function __construct($int) {}
}