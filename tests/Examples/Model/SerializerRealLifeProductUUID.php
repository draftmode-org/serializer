<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Model;

class SerializerRealLifeProductUUID extends SerializerRealLifeUUID {

    public function __construct(string $value = null) {
        if (strlen($value) > 3) {
            throw new \InvalidArgumentException("maxLen: 3, given ".strlen($value));
        }
        parent::__construct($value);
    }
}