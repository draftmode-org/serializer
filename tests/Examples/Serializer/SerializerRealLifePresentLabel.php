<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Serializer;
use Terrazza\Component\Serializer\INameConverter;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductLabel;

class SerializerRealLifePresentLabel implements INameConverter {
    private SerializerRealLifeProductLabel $value;

    public function __construct(SerializerRealLifeProductLabel $value) {
        $this->value = $value;
    }

    public function getValue() : string {
        return $this->value->getValue();
    }
}