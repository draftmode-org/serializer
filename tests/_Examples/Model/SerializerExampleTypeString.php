<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerExampleTypeString {
    public string $string;
    public function __construct(string $string) {
        $this->string = $string;
    }

    /**
     * @param string $string
     */
    public function setString(string $string): void
    {
        $this->string = $string;
    }


}