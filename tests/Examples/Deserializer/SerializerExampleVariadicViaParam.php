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
}