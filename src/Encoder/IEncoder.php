<?php
namespace Terrazza\Component\Serializer\Encoder;

interface IEncoder {
    /**
     * @param array $data
     * @return mixed
     */
    function encode(array $data);
}