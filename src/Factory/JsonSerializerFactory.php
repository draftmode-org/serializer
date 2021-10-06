<?php

namespace Terrazza\Component\Serializer\Factory;

use ReflectionException;
use Terrazza\Component\ReflectionClass\ClassName\ReflectionClassClassName;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\DecoderInterface;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonSerializerFactory implements SerializerInterface {
    private DecoderInterface $decoder;
    private DenormalizerInterface $denormalizer;

    public function __construct() {
        $this->decoder                              = new JsonDecoder();
        $this->denormalizer                         = new ArrayDenormalizer(
                new ReflectionClassClassName()
        );
    }

    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @return T
     * @throws ReflectionException
     * @template T
     */
    public function deserialize(string $className, $input): object {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input);
    }
}