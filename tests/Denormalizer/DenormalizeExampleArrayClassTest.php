<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleArrayAsClass;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeInt;

class DenormalizeExampleArrayClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testCreate() {
        $input                                      = [
            'array' => [$i1 = 1,$i2 = 2]
        ];
        $deserializer                               = Denormalizer::get();
        $object                                     = $deserializer->denormalizeClass(SerializerExampleArrayAsClass::class, $input);
        /*$deserializer                               = Denormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'array' => [$i3 = 3]
        ]);*/
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            //[new SerializerExampleTypeInt($i3)]
        ],[
            $object->array,
            //$objectUpdate->array,
        ]);
    }
}