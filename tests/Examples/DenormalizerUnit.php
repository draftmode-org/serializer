<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;

class DenormalizerUnit {
    public static function get(bool $log=false) : DenormalizerInterface {
        $logger = LoggerUnit::getLogger("ArrayDenormalizer", $log);
        return new ArrayDenormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
    }
}