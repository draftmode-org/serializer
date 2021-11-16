<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleVariadicViaParam {
    /** @var array|SerializerExampleTypeInt[]  */
    public array $int;

    /**
     * @param array|SerializerExampleTypeInt[] $int
     */
    public function __construct(...$int) {
        $this->int = $int;
    }

    /**
     * @param array|SerializerExampleTypeInt[] $int
     */
    public function setInt(array $int): void
    {
        $this->int = $int;
    }
}