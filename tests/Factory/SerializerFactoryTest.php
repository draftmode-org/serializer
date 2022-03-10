<?php
namespace Terrazza\Component\Serializer\Tests\Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\Tests\_Mocks\ConverterFactory;

class SerializerFactoryTest extends TestCase {

    function testAddEncoder() {
        $serializer = ConverterFactory::getSerializer();
        $serializer->addEncoder("my", new JsonEncoder());
        $this->assertTrue(true);
    }

    function testUnknownEncoder() {
        $serializer = ConverterFactory::getSerializer();
        $this->expectException(RuntimeException::class);
        $serializer->serialize(new SerializerFactoryTestClass, "unknown", null);
    }
}

class SerializerFactoryTestClass {}