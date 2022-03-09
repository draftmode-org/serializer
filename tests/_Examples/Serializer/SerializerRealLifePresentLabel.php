<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Serializer;
use Terrazza\Component\Serializer\INormalizerNameConverter;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductLabel;

class SerializerRealLifePresentLabel implements INormalizerNameConverter {
    private SerializerRealLifeProductLabel $value;

    public function __construct(SerializerRealLifeProductLabel $value) {
        $this->value = $value;
    }

    public function getValue() : string {
        return $this->value->getValue();
    }
}