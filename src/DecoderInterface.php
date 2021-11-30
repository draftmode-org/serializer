<?php

namespace Terrazza\Component\Serializer;

interface DecoderInterface {
    /**
     * @param mixed $data
     * @param bool $nullable
     * @return mixed
     */
    function decode($data, bool $nullable=false);
}