<?php
namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Denormalizer\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\Factory\Json\JsonArraySerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleSimple;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerExampleTypeInt;

class JsonArraySerializerExceptionTest extends TestCase {
    private function getSerializer() : SerializerInterface {
        return new JsonArraySerializer();
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
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize("SerializerExampleConstructorSimple::class", $input);
    }

    function testUnknownClass2() {
        $input                                      = json_encode([]);
        $serializer                                 = new ArrayDenormalizer(
            new AnnotationFactory(new ClassNameResolver())
        );
        $this->expectException(InvalidArgumentException::class);
        $serializer->denormalize(1, $input);
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

    function testMultipleAnnotations() {
        $input                                      = json_encode(["value" => "1"]);
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(DeserializerExceptionMultipleAnnotation::class, $input);
    }

    function testProtectedMethod() {
        $serializer                                 = $this->getSerializer();
        $serializer                                 = $serializer->withAllowedAccess(3);
        $serializer->deserialize(DeserializerExceptionProtectedMethod::class, json_encode(["value" => 1]));
        $this->assertTrue(true);
    }

    function testProtectedMethodException() {
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(DeserializerExceptionProtectedMethod::class, json_encode(["value" => 1]));
    }

    function testPrivateMethod() {
        $serializer                                 = $this->getSerializer();
        $serializer                                 = $serializer->withAllowedAccess(3);
        $serializer->deserialize(DeserializerExceptionPrivateMethod::class, json_encode(["value" => 1]));
        $this->assertTrue(true);
    }

    function testPrivateMethodException() {
        $serializer                                 = $this->getSerializer();
        $this->expectException(InvalidArgumentException::class);
        $serializer->deserialize(DeserializerExceptionPrivateMethod::class, json_encode(["value" => 1]));
    }
}

class DeserializerExceptionMultipleAnnotation {
    /**
     * @param int[]|string[] $value
     */
    public function setValue(array $value){}
}
class DeserializerExceptionProtectedMethod {
    protected function setValue(int $value){}
}
class DeserializerExceptionPrivateMethod {
    private function setValue(int $value){}
}