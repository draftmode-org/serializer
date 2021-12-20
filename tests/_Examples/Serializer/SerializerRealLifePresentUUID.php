<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Serializer;
use Terrazza\Component\Serializer\INameConverter;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeUUID;

class SerializerRealLifePresentUUID implements INameConverter {
    private SerializerRealLifeUUID $value;

    public function __construct(SerializerRealLifeUUID $value) {
        $this->value = $value;
    }

    public function getValue() :?string {
        return $this->value->getValue();
    }
}