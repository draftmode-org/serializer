<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Serializer;
use Terrazza\Component\Serializer\NameConverterInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUUID;

class SerializerRealLifePresentAmount implements NameConverterInterface {
    private SerializerRealLifeProductAmount $value;

    public function __construct(SerializerRealLifeProductAmount $value) {
        $this->value = $value;
    }

    public function getValue() :?float {
        return $this->value->getValue();
    }
}