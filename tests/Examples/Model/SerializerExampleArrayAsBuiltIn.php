<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Model;

class SerializerExampleArrayAsBuiltIn {
    /** @var array|int[]  */
    public array $array;

    /**
     * @param array|int[] $array
     */
    public function __construct(array $array) {
        $this->array = $array;
    }

    /**
     * @param array|int[] $array
     */
    public function setArray(array $array): void
    {
        $this->array = $array;
    }
}