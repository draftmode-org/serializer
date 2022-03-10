<?php
namespace Terrazza\Component\Serializer\Tests\Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\Tests\_Mocks\ConverterFactory;

class DeserializerFactoryTest extends TestCase {

    function testAddDecoder() {
        $deserializer = ConverterFactory::getDeserializer();
        $deserializer->addDecoder("my", new JsonDecoder());
        $this->assertTrue(true);
    }

    function testUnserializeNull() {
        $deserializer   = ConverterFactory::getDeserializer();
        $object         = $deserializer->deserialize(UnserializerFactoryTestClass::class, "json", null);
        $this->assertInstanceOf(UnserializerFactoryTestClass::class, $object);
    }

    function testUnknownDecoder() {
        $deserializer   = ConverterFactory::getDeserializer();
        $this->expectException(RuntimeException::class);
        $deserializer->deserialize(SerializerFactoryTestClass::class, "unknown", null);
    }
}

class UnserializerFactoryTestClass {}