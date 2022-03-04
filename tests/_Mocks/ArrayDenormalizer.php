<?php

namespace Terrazza\Component\Serializer\Tests\_Mocks;

use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer as Denormalizer;
use Terrazza\Component\Serializer\IDenormalizer;

class ArrayDenormalizer {
    public static function get($stream=null) : IDenormalizer {
        $logger = Logger::get($stream);
        return new Denormalizer(
            $logger,
            AnnotationFactory::get($logger)
        );
    }
}