<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use InvalidArgumentException;
use ReflectionException;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\ReflectionClass\ClassName;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\DecoderInterface;
use Terrazza\Component\Serializer\Denormalizer\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonArraySerializer implements SerializerInterface {
    private DecoderInterface $decoder;
    private DenormalizerInterface $denormalizer;

    public function __construct(LogInterface $logger) {
        $this->decoder                              = new JsonDecoder();
        $this->denormalizer                         = new ArrayDenormalizer(
            $logger,
            new AnnotationFactory(
                new ClassName()
            )
        );
    }

    /**
     * @param int $allowedAccess
     * @return SerializerInterface
     */
    public function withAllowedAccess(int $allowedAccess) : SerializerInterface {
        $serializer                                     = clone $this;
        $serializer->denormalizer                       = $serializer->denormalizer->withAllowedAccess($allowedAccess);
        return $serializer;
    }

    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input): object {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input);
    }
}