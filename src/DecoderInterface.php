<?php
namespace Terrazza\Component\Serializer;
use Terrazza\Component\Serializer\Decoder\Exception\DecoderException;

interface DecoderInterface {
    /**
     * @param mixed $data
     * @return array|null
     * @throws DecoderException
     */
    function decode($data) :?array;
}