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
        $data                                       = $this->decoder->decode($input);
        $className                                  = $this->cloneClass($className);
        return $this->denormalizer->denormalize($className, $data, $restrictUnInitialized, $restrictArguments);
    }

    /**
     * @param class-string<T>|T $className
     * @return T
     * @template T of object
     */
    private function cloneClass($className) {
        return (is_object($className)) ? unserialize(serialize($className)) : $className;
    }
}