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
     * @param class-string<T>|object $className
     * @param mixed $input
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input) : object {
        $data                                       = $this->decoder->decode($input);
        $className                                  = $this->cloneClass($className);
        return $this->denormalizer->denormalize($className, $data);
    }

    /**
     * @param class-string<T>|object $className
     * @return T
     * @template T
     */
    private function cloneClass($className) {
        return (is_object($className)) ? unserialize(serialize($className)) : $className;
    }
}