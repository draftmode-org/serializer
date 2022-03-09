<?php

namespace Terrazza\Component\Serializer\Factory;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\IDecoder;
use Terrazza\Component\Serializer\Denormalizer;
use Terrazza\Component\Serializer\IDenormalizer;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\IDeserializer;

class JsonDeserializer implements IDeserializer {
    private IDecoder $decoder;
    private IDenormalizer $denormalizer;

    public function __construct(LoggerInterface $logger) {
        $this->decoder                              = new JsonDecoder();
        $this->denormalizer                         = new Denormalizer($logger);
    }

    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input, bool $restrictArguments=false) {
        return (new Deserializer($this->decoder, $this->denormalizer))
            ->deserialize($className, $input, $restrictArguments);
    }
}