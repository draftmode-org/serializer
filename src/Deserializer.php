<?php
namespace Terrazza\Component\Serializer;
use InvalidArgumentException;
use ReflectionException;

/**
 * 1. decode an input into an array (use IDecoder)
 * 2. denormalize array into a class
 */
class Deserializer implements DeserializerInterface {
    private DenormalizerInterface $denormalizer;
    private DecoderInterface $decoder;

    /**
     * @param DecoderInterface $decoder
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DecoderInterface $decoder, DenormalizerInterface $denormalizer) {
        $this->decoder                              = $decoder;
        $this->denormalizer                         = $denormalizer;
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
    public function deserialize(string $className, $input, bool $restrictArguments=false) {
        $data                                       = $this->decoder->decode($input);
        return $this->denormalizer->denormalizeClass($className, $data, $restrictArguments);
    }
}