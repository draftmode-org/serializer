<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeInt;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicAsClass;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleVariadicViaParam;

class DenormalizeExampleVariadicClassTest extends TestCase {

    /**
     * @throws ReflectionException
     */
    function testCreate() {
        $input                                      = [
            'int' => [$i1 = 1, $i2 = 2]
        ];
        $deserializer                               = Denormalizer::get();
        $object                                     = $deserializer->denormalizeClass(SerializerExampleVariadicAsClass::class, $input);
        /*$objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 3]
        ]);*/
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            //[new SerializerExampleTypeInt($i3)],
        ],[
            $object->int,
            //$objectUpdate->int,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    function testCreateVariadicViaParam() {
        $input                                      = [
            'int' => [$i1 = 2,$i2 = 3]
        ];
        $deserializer                               = Denormalizer::get();
        $object                                     = $deserializer->denormalizeClass(SerializerExampleVariadicViaParam::class, $input);
        /*$deserializer                               = Denormalizer::get();
        $objectUpdate                               = $deserializer->denormalize($object, [
            'int' => [$i3 = 4]
        ]);*/
        $this->assertEquals([
            [new SerializerExampleTypeInt($i1), new SerializerExampleTypeInt($i2)],
            //[new SerializerExampleTypeInt($i3)]
        ],[
            $object->int,
            //$objectUpdate->int,
        ]);
    }
}