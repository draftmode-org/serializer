<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Serializer;
use DateTime;
use Terrazza\Component\Serializer\NameConverterInterface;

class SerializerRealLifePresentDateTime implements NameConverterInterface {
    private ?DateTime $value;

    public function __construct(?DateTime $value) {
        $this->value = $value;
    }

    public function getValue() :?string {
        return $this->value ? $this->value->format("Y-m-d") : null;
    }
}