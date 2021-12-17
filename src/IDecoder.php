<?php

namespace Terrazza\Component\Serializer;

interface IDecoder {
    /**
     * @param mixed $data
     * @param bool $nullable
     * @return mixed
     */
    function decode($data, bool $nullable=false);
}