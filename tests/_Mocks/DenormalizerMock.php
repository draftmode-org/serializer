<?php

namespace Terrazza\Component\Serializer\Tests\_Mocks;

use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\IDenormalizer;

class DenormalizerMock {
    public static function get(bool $log=false) : IDenormalizer {
        $logger = LoggerMock::get($log);
        return new ArrayDenormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
    }
}