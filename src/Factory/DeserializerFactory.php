<?php
namespace Terrazza\Component\Serializer\Factory;

use Psr\Log\LoggerInterface;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\Serializer\Decoder\DecoderFactory;
use Terrazza\Component\Serializer\DecoderInterface;
use Terrazza\Component\Serializer\Denormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Deserializer;
use Terrazza\Component\Serializer\DeserializerFactoryInterface;

/**
 * 1. decode an input to an array (use IDecoderFactory to get Decoder for contentType)
 * 2. denormalize array into a class
 */
class DeserializerFactory implements DeserializerFactoryInterface {
    private DecoderFactory $decoderFactory;
    private DenormalizerInterface $denormalizer;
    public function __construct(LoggerInterface $logger) {
        $this->decoderFactory                       = new DecoderFactory($logger);
        $this->denormalizer                         = new Denormalizer($logger);
    }

    /**
     * @param string $contentType
     * @param DecoderInterface $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, DecoderInterface $decoder, string $contentTypePattern=null) : void {
        $this->decoderFactory->addDecoder($contentType, $decoder, $contentTypePattern);
    }

    /**
     * @param class-string<T> $className
     * @param string $contentType
     * @param string|null $content
     * @param bool $restrictArguments
     * @return T|null
     * @throws ReflectionException
     * @template T of object
     */
    public function deserialize(string $className, string $contentType, ?string $content, bool $restrictArguments=false) {
        if ($decoder = $this->decoderFactory->getDecoder($contentType)) {
            $unserialize = new Deserializer($decoder, $this->denormalizer);
            return $unserialize->deserialize($className, $content, $restrictArguments);
        } else {
            throw new RuntimeException("no decoder for contentType found, given ".$contentType);
        }
    }
}