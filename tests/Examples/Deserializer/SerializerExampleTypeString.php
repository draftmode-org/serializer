<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleTypeString {
    public string $string;
    public function __construct(string $string) {
        $this->string = $string;
    }
}