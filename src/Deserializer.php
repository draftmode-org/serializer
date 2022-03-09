<?php
namespace Terrazza\Component\Serializer;
use InvalidArgumentException;
use ReflectionException;
use Terrazza\Component\Serializer\Decoder\IDecoder;

/**
 * 1. decode an input into an array (use IDecoder)
 * 2. denormalize array into a class
 */
class Deserializer implements IDeserializer {
    private IDenormalizer $denormalizer;
    private IDecoder $decoder;

    /**
     * @param IDecoder $decoder
     * @param IDenormalizer $denormalizer
     */
    public function __construct(IDecoder $decoder, IDenormalizer $denormalizer) {
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