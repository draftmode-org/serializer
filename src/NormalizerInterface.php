<?php
namespace Terrazza\Component\Serializer;

interface NormalizerInterface {
    /**
     * @param object $object
     * @return array
     */
    public function normalize(object $object) : array;
}