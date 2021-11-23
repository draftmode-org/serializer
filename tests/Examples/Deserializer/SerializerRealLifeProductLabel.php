<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

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