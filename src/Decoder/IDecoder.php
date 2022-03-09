<?php
namespace Terrazza\Component\Serializer\Decoder;
use Terrazza\Component\Serializer\Decoder\Exception\DecoderException;

interface IDecoder {
    /**
     * @param string|null $data
     * @return array|null
     * @throws DecoderException
     */
    function decode(?string $data) :?array;
}