<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Model;

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
}