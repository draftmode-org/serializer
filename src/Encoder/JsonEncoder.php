<?php

namespace Terrazza\Component\Serializer\Encoder;
use Terrazza\Component\Serializer\IEncoder;

class JsonEncoder implements IEncoder {
    /**
     * @param array $data
     * @return string
     */
    function encode(array $data) : string {
        return json_encode($data);
    }
}