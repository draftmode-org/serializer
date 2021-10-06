<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleTypeFloat {
    public float $float;
    public function __construct(float $float) {
        $this->float = $float;
    }
}