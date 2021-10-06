<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleArrayAsBuiltIn {
    /** @var array|int[]  */
    public array $array;

    /**
     * @param array|int[] $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }
}