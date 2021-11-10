<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use ReflectionException;
use Terrazza\Component\ReflectionClass\ClassName;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\DecoderInterface;
use Terrazza\Component\Serializer\Denormalizer\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayConstructorDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonArrayConstructorSerializer implements SerializerInterface {
    private DecoderInterface $decoder;
    private DenormalizerInterface $denormalizer;

    public function __construct() {
        $this->decoder                              = new JsonDecoder();
        $this->denormalizer                         = new ArrayConstructorDenormalizer(
            new AnnotationFactory(
                new ClassName()
            )
        );
    }

    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @return T
     * @throws ReflectionException
     * @template T
     */
    public function deserialize($className, $input): object {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input);
    }
}