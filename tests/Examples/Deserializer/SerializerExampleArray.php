<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleArray {
    /** @var array  */
    public array $array;

    public function __construct(array $array) {
        $this->array = $array;
    }

    /**
     * @param array $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }
}