<?php

namespace Terrazza\Component\Serializer;

interface IEncoder {
    /**
     * @param array $data
     * @return mixed
     */
    function encode(array $data);
}