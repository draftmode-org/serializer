<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\IDecoder;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\IDenormalizer;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\IDeserializer;

class JsonDeserializer implements IDeserializer {
    private IDecoder $decoder;
    private IDenormalizer $denormalizer;

    public function __construct(LoggerInterface $logger) {
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
     * @param class-string<T>|T $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input, $restrictUnInitialized, $restrictArguments);
    }
}