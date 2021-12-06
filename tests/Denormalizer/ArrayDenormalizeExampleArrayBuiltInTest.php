<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\DenormalizerUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleArrayAsBuiltIn;

class ArrayDenormalizeExampleArrayBuiltInTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function test() {
        $input                                      = [
            'array' => [$i1 = 1,$i2 = 2]
        ];
        $deserializer                               = DenormalizerUnit::get();
        $object                                     = $deserializer->denormalize(SerializerExampleArrayAsBuiltIn::class, $input);
        $deserializer                               = DenormalizerUnit::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'array' => [$i3 = 3]
        ]);
        $this->assertEquals([
            [$i1, $i2],
            [$i3],
        ],[
            $object->array,
            $objectUpdate->array
        ]);
    }
}