<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use InvalidArgumentException;
use ReflectionException;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\DecoderInterface;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\DeserializerInterface;

class JsonDeserializer implements DeserializerInterface {
    private DecoderInterface $decoder;
    private DenormalizerInterface $denormalizer;

    public function __construct(LogInterface $logger) {
        $this->decoder                              = new JsonDecoder();
        $this->denormalizer                         = new ArrayDenormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
    }

    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false): object {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input, $restrictUnInitialized, $restrictArguments);
    }
}