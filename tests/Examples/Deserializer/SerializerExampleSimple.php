<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleSimple {
    public int $number;
    public ?int $number2;
    public float $float;
    public string $string;
    public array $array;
    public int $dInt;

    /**
     * @param int $number
     */
    public function __construct($number, ?int $number2, float $float, string $string, array $array, int $dInt=2) {
        $this->number = $number;
        $this->number2 = $number2;
        $this->float = $float;
        $this->string = $string;
        $this->array = $array;
        $this->dInt = $dInt;
    }
}