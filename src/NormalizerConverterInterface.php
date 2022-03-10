<?php

namespace Terrazza\Component\Serializer;

interface NormalizerConverterInterface {
    /**
     * @return mixed
     */
    public function getValue();
}