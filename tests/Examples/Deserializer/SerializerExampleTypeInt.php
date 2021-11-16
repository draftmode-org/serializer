<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleTypeInt {
    /**
     * @var int
     */
    public int $number;
    public function __construct(int $number) {
        $this->number = $number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }
}