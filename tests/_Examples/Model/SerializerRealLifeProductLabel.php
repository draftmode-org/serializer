<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerRealLifeProductLabel {
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}