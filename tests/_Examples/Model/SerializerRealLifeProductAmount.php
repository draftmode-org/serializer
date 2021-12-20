<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

use InvalidArgumentException;

class SerializerRealLifeProductAmount {
    private ?float $value;

    /**
     * @param float|null $value
     */
    public function __construct(float $value=null) {
        if ($value > 100) {
            throw new InvalidArgumentException("amount has to be less than 100, given ".$value);
        }
        $this->value = $value;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }
}