<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\ArrayDenormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleSimple;

class ArrayDenormalizeExampleSimpleTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'number' => $number = 1,
            'float' => $float = 1.2,
            'string' => $string = "string",
            'array' => $array = [1,2]
        ];
        $deserializer                               = ArrayDenormalizer::get();
        $object                                     = $deserializer->denormalize(SerializerExampleSimple::class, $input);
        $deserializer                               = ArrayDenormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            "number" => $numberUpdate = 3,
            "float" => $floatUpdate = 3.1,
        ]);
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
            $object->dInt,                          // has default value

            $objectUpdate->number,
            $objectUpdate->float,
        ]);
    }
}