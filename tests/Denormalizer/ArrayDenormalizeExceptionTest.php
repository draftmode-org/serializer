<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\Serializer\Tests\_Mocks\DenormalizerMock;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicViaParam;

class ArrayDenormalizeExceptionTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testUnknownClass() {
        $input                                      = [];
        $deserializer                               = DenormalizerMock::get();
        $this->expectException(RuntimeException::class);
        $deserializer->denormalize("SerializerExampleConstructorSimple::class", $input);
    }

    function testInvalidArgumentIsArrayExpectArray() {
        $input                                      = json_encode(['int' => [$i1 = 2,$i2 = 3]]);
        $deserializer                               = DenormalizerMock::get();
        $this->expectException(InvalidArgumentException::class);
        $deserializer->denormalize(SerializerExampleVariadicViaParam::class, $input);
    }



/*
    function testRequiredArgument() {
        $input                                      = json_encode([]);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $this->expectException(InvalidArgumentException::class);
        $deserializer->deserialize(SerializerExampleSimple::class, $input);
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
*/
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