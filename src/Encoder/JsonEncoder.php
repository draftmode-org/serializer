<?php

namespace Terrazza\Component\Serializer\Encoder;
use Terrazza\Component\Serializer\EncoderInterface;

class JsonEncoder implements EncoderInterface {
    /**
     * @param array $data
     * @return string
     */
    function encode(array $data) : string {
        return json_encode($data);
    }
}