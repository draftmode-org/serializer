<?php
namespace Terrazza\Component\Serializer\Tests\_Examples\Model;

class SerializerExampleArrayAsClass {
    /** @var SerializerExampleTypeInt[]  */
    public array $array;

    /**
     * @param array|SerializerExampleTypeInt[] $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }

    /**
     * @param SerializerExampleTypeInt[] $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }
}