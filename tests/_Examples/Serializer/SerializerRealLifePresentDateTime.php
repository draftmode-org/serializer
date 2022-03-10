<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Serializer;
use DateTime;
use Terrazza\Component\Serializer\NormalizerConverterInterface;

class SerializerRealLifePresentDateTime implements NormalizerConverterInterface {
    private ?DateTime $value;

    public function __construct(?DateTime $value) {
        $this->value = $value;
    }

    public function getValue() :?string {
        return $this->value ? $this->value->format("Y-m-d") : null;
    }
}