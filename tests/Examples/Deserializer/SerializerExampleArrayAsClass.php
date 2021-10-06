<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleArrayAsClass {
    /** @var SerializerExampleTypeInt[]  */
    public array $array;

    /**
     * @param array|SerializerExampleTypeInt[] $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }
}