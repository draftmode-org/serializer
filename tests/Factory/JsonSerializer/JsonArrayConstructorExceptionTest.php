<?php
namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Factory\Json\JsonArrayConstructorSerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleSimple;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeInt;

class JsonSerializerFactoryConstructorExceptionTest extends TestCase {
    private function getSerializer() : SerializerInterface {
        return new JsonArrayConstructorSerializer();
    }
    function testRequiredArgument() {
        $input                                      = json_encode([]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(SerializerExampleSimple::class, $input);
    }

    function testUnknownClass() {
        $input                                      = json_encode([]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(ReflectionException::class);
        $serializer->deserialize("SerializerExampleConstructorSimple::class", $input);
    }

    function testMissingConstructor() {
        $input                                      = json_encode([]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(JsonSerializerFactoryConstructorExceptionNoConstructor::class, $input);
    }

    function testMultipleAnnotations() {
        $input                                      = json_encode([
            "int" => 1
        ]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(JsonSerializerFactoryConstructorExceptionMultipleAnnotation::class, $input);
    }

    function testInvalidTypeStringInt() {
        $input                                      = json_encode(["number" => "string"]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(SerializerExampleTypeInt::class, $input);
    }

    function testInvalidDateTimeDate() {
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(SerializerExampleTypeDateTime::class, json_encode(
            ["date" => "31.02.2021"]));
    }

    function testInvalidDateTimeValue() {
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(SerializerExampleTypeDateTime::class, json_encode(
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