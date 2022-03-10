<?php
namespace Terrazza\Component\Serializer;

interface NormalizerInterface {
    /**
     * @param object $object
     * @return array
     */
    public function normalize(object $object) : array;

    /**
     * @param array|NormalizerConverterInterface[] $nameConverter
     * @return NormalizerInterface
     */
    public function withNameConverter(array $nameConverter) : NormalizerInterface;
}