<?php

namespace Terrazza\Component\Serializer;

interface DecoderInterface {
    /**
     * @param $data
     * @param bool $nullable
     * @return mixed
     */
    function decode($data, bool $nullable=false);
}