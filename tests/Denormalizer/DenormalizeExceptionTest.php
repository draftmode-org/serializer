<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicViaParam;

class ArrayDenormalizeExceptionTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testUnknownClass() {
        $input                                      = [];
        $deserializer                               = Denormalizer::get();
        $this->expectException(RuntimeException::class);
        $deserializer->denormalize("SerializerExampleConstructorSimple::class", $input);
    }

    function testInvalidArgumentIsArrayExpectArray() {
        $input                                      = json_encode(['int' => [$i1 = 2,$i2 = 3]]);
        $deserializer                               = Denormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $deserializer->denormalize(SerializerExampleVariadicViaParam::class, $input);
    }
}