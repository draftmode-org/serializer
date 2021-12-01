<?php

namespace Terrazza\Component\Serializer\Tests\Examples\Model;

class SerializerRealLifeUUID {
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