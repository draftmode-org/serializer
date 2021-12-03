<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifePerson;

class JsonDeserializerExampleClassInClassTest extends TestCase {
    /**
     * @throws ReflectionException
     */
    function testBuildCompletelyAndUpdate() {
        $input                                      = json_encode([
            'person'        => [
                'name'          => $mPersonName         = "mPersonName",
                'address'       => [
                    'street'        => $mAddressStreet  = "mAddressStreet",
                    'city'          => $mAddressCity    = "mAddressCity",
                ]
            ]
        ]);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(JsonDeserializerExampleClassInClass::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode([
            'person'        => [
                'name'      => $uPersonName = "uPersonName",
                'address'   => [
                    'street'        => $uAddressStreet = "uAddressStreet"
                ]
            ]
        ]));
        $this->assertEquals([
            $mPersonName,
            $mAddressStreet,
            $mAddressCity,
            $uPersonName,
            $uAddressStreet,
            $mAddressCity,
        ],[
            $object->getPerson()->getName(),
            $object->getPerson()->getAddress()->getStreet(),
            $object->getPerson()->getAddress()->getCity(),
            $objectUpdate->getPerson()->getName(),
            $objectUpdate->getPerson()->getAddress()->getStreet(),
            $objectUpdate->getPerson()->getAddress()->getCity(),
        ]);
    }

    /**
     * @throws ReflectionException
     */
    function testBuildPartialCreateAndUpdate() {
        $input                                      = json_encode([
            'person'        => [
                'name'          => $mPersonName = "mPersonName"
            ]
        ]);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $object                                     = $deserializer->deserialize(JsonDeserializerExampleClassInClass::class, $input);
        $deserializer                               = JsonArrayUnit::getDeserializer();
        $objectUpdate                               = $deserializer->deserialize($object, json_encode([
            'person'        => [
                'address'       => [
                    'street'        => $uAddressStreet = "uAddressStreet",
                    'city'          => $uAddressCity = "uAddressCity",
                ]
            ]
        ]));
        $this->assertEquals([
            $mPersonName,
            null,

            $mPersonName,
            $uAddressStreet,
            $uAddressCity,
        ],[
            $object->getPerson()->getName(),
            $object->getPerson()->getAddress(),

            $objectUpdate->getPerson()->getName(),
            $objectUpdate->getPerson()->getAddress()->getStreet(),
            $objectUpdate->getPerson()->getAddress()->getCity(),
        ]);
    }
}

class JsonDeserializerExampleClassInClass {
    private SerializerRealLifePerson $person;

    /**
     * @param SerializerRealLifePerson $person
     */
    public function setPerson(SerializerRealLifePerson $person): void
    {
        $this->person = $person;
    }

    /**
     * @return SerializerRealLifePerson
     */
    public function getPerson(): SerializerRealLifePerson
    {
        return $this->person;
    }
}