<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerRealLifeUserUUID {
    private ?string $value;

    /**
     * @param string|null $value
     */
    public function __construct(string $value=null) {
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}