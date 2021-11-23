<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerRealLifeProductUUID {
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value) {
        if (strlen($value) > 3) {
            throw new \InvalidArgumentException("maxLen: 3, given ".strlen($value));
        }
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