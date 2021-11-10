<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleSimple {
    public int $number;
    public ?int $number2=null;
    public float $float;
    public string $string;
    public array $array;
    public int $dInt=2;

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

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @param int|null $number2
     */
    public function setNumber2(?int $number2): void
    {
        $this->number2 = $number2;
    }

    /**
     * @param float $float
     */
    public function setFloat(float $float): void
    {
        $this->float = $float;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }

    /**
     * @param array $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }

    /**
     * @param int $dInt
     */
    public function setDInt(int $dInt=2): void
    {
        $this->dInt = $dInt;
    }



}