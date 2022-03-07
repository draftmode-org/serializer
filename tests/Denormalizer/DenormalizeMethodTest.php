<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifePerson;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;

class DenormalizeMethodTest extends TestCase {

    function testMethodFailure() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $this->expectException(\RuntimeException::class);
        $denormalizer->denormalizeMethodValues($object, "methodUnknown", []);
    }

    function testValueTypeFailure() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $this->expectException(\InvalidArgumentException::class);
        $denormalizer->denormalizeMethodValues($object, "method1", ["id" => "string"]);
    }

    function testRequiredArgumentFailure() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $this->expectException(\InvalidArgumentException::class);
        $denormalizer->denormalizeMethodValues($object, "method2", ["id" => 12]);
    }

    function testRestrictedArgument() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $this->expectException(\InvalidArgumentException::class);
        $denormalizer->denormalizeMethodValues($object, "method1", ["id" => 12, "invalidKey" => "value"], true);
    }

    function testRestrictedArguments() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $this->expectException(\InvalidArgumentException::class);
        $denormalizer->denormalizeMethodValues($object, "method1", ["id" => 12, "invalidKey1" => "value", "invalidKey2" => "value"], true);
    }

    function test() {
        $denormalizer   = Denormalizer::get();
        $object         = new DenormalizeMethodTestController;
        $method1        = $denormalizer->denormalizeMethodValues($object, "method1", ["id" => $id = 12, "k" => 12]);
        $method2        = $denormalizer->denormalizeMethodValues($object, "method2", ["id" => $id, "name" => $name = "my"]);
        $method3        = $denormalizer->denormalizeMethodValues($object, "method3", ["id" => $id, "name" => $name, "person" => $person = [
            "name" => "myName",
            "address" => [
                "street" => "street", "city" => "city", "zip" => 1210
            ]]
        ]);
        $this->assertEquals([
            [$id],
            [$id, $name],
            [$id, $name, $denormalizer->denormalize(SerializerRealLifePerson::class, $person)],
        ],[
            $method1,
            $method2,
            $method3,
        ]);
    }
}

class DenormalizeMethodTestController {
    function method1(int $id) {}
    function method2(int $id, string $name) {}
    function method3(int $id, string $name, SerializerRealLifePerson $person) {}
}