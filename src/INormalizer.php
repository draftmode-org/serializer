<?php
namespace Terrazza\Component\Serializer;

interface INormalizer {
    /**
     * @param object $object
     * @return array
     */
    public function normalize(object $object) : array;

    /**
     * @param array $nameConverter
     * @return INormalizer
     */
    public function withNameConverter(array $nameConverter) : INormalizer;
}