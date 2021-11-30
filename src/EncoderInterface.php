<?php

namespace Terrazza\Component\Serializer;

interface EncoderInterface {
    /**
     * @param array $data
     * @return mixed
     */
    function encode(array $data);
}