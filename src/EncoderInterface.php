<?php
namespace Terrazza\Component\Serializer;

interface EncoderInterface {
    /**
     * @param array|null $data
     * @return string
     */
    function encode(?array $data) : string;
}