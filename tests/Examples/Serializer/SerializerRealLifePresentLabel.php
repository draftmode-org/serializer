<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Serializer;
use Terrazza\Component\Serializer\NameConverterInterface;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductLabel;

class SerializerRealLifePresentLabel implements NameConverterInterface {
    private SerializerRealLifeProductLabel $value;

    public function __construct(SerializerRealLifeProductLabel $value) {
        $this->value = $value;
    }

    public function getValue() : string {
        return $this->value->getValue();
    }
}