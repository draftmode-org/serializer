<?php
namespace Terrazza\Component\Serializer;
use InvalidArgumentException;
use ReflectionException;

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
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize(string $className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) {
        $data                                       = $this->decoder->decode($input);
        //$className                                  = $this->cloneClass($className);
        return $this->denormalizer->denormalize($className, $data, $restrictUnInitialized, $restrictArguments);
    }

    /*
    private function cloneClass($className) {
        return (is_object($className)) ? unserialize(serialize($className)) : $className;
    }*/
}