<?php
namespace Terrazza\Component\Serializer;

use Psr\Log\LoggerInterface;
use ReflectionException;
use Terrazza\Component\Serializer\Decoder\DecoderFactory;
use Terrazza\Component\Serializer\Decoder\IDecoder;

/**
 * 1. decode an input to an array (use IDecoderFactory to get Decoder for contentType)
 * 2. denormalize array into a class
 */
class DeserializerFactory implements IDeserializerFactory {
    private DecoderFactory $decoderFactory;
    private IDenormalizer $denormalizer;
    public function __construct(LoggerInterface $logger) {
        $this->decoderFactory                       = new DecoderFactory($logger);
        $this->denormalizer                         = new Denormalizer($logger);
    }

    /**
     * @param string $contentType
     * @param IDecoder $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, IDecoder $decoder, string $contentTypePattern=null) : void {
        $this->decoderFactory->addDecoder($contentType, $decoder, $contentTypePattern);
    }

    /**
     * @param class-string<T> $className
     * @param mixed $content
     * @param string $contentType
     * @param bool $restrictArguments
     * @return T|null
     * @throws ReflectionException
     * @template T of object
     */
    public function deserialize(string $className, $content, string $contentType, bool $restrictArguments=false) {
        if ($input = $this->decoderFactory->decode($content, $contentType)) {
            return $this->denormalizer->denormalizeClass($className, $input, $restrictArguments);
        } else {
            return null;
        }
    }
}