<?php
namespace Terrazza\Component\Serializer;
use InvalidArgumentException;
use ReflectionException;

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
     * @param object|string $instance
     * @param mixed $input
     * @return object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($instance, $input) : object {
        $data                                       = $this->decoder->decode($input);
        return $this->denormalizer->denormalize($instance, $data);
    }
}