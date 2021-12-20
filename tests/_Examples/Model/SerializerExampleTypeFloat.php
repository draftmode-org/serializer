<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerExampleTypeFloat {
    public float $float;
    public function __construct(float $float) {
        $this->float = $float;
    }

    /**
     * @param float $float
     */
    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    /**
     * @return float
     */
    public function getFloat(): float
    {
        return $this->float;
    }
}