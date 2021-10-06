<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleTypeInt {
    public int $number;
    public function __construct(int $number) {
        $this->number = $number;
    }
}