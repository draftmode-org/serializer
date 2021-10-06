<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleVariadicBuiltIn {
    /** @var array|int[]  */
    public array $int;

    public function __construct(int ...$int) {
        $this->int = $int;
    }
}