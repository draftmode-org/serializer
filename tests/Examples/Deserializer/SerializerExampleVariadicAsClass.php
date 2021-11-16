<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;

class SerializerExampleVariadicAsClass {
    /** @var array|SerializerExampleTypeInt[]  */
    public array $int;

    public function __construct(SerializerExampleTypeInt ...$int) {
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