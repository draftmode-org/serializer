<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;
use Terrazza\Component\Serializer\INormalizer;
use Terrazza\Component\Serializer\Normalizer\ArrayNormalizer as Normalizer;

class ArrayNormalizer {
    function get($stream=null) : INormalizer {
        $logger = Logger::get($stream);
        return new Normalizer(
            $logger,
            AnnotationFactory::get($logger)
        );
    }
}