<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Serializer;
use Terrazza\Component\Serializer\INameConverter;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductAmount;

class SerializerRealLifePresentAmount implements INameConverter {
    private SerializerRealLifeProductAmount $value;

    public function __construct(SerializerRealLifeProductAmount $value) {
        $this->value = $value;
    }

    public function getValue() :?float {
        return $this->value->getValue();
    }
}