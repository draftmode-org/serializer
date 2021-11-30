<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Serializer;
use Terrazza\Component\Serializer\NameConverterInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUUID;

class SerializerRealLifePresentUUID implements NameConverterInterface {
    private SerializerRealLifeUUID $value;

    public function __construct(SerializerRealLifeUUID $value) {
        $this->value = $value;
    }

    public function getValue() :?string {
        return $this->value->getValue();
    }
}