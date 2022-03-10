<?php
namespace Terrazza\Component\Serializer\Factory;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Terrazza\Component\Serializer\Encoder\EncoderFactory;
use Terrazza\Component\Serializer\EncoderInterface;
use Terrazza\Component\Serializer\Normalizer;
use Terrazza\Component\Serializer\NormalizerInterface;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\SerializerFactoryInterface;

/**
 * 1. normalize an object into an array
 * 2. encode an array into json/xml/...
 */
class SerializerFactory implements SerializerFactoryInterface {
    private EncoderFactory $encoderFactory;
    private NormalizerInterface $normalizer;
    private ?array $nameConverter;
    public function __construct(LoggerInterface $logger, array $nameConverter=null) {
        $this->encoderFactory                       = new EncoderFactory($logger);
        $this->normalizer                           = new Normalizer($logger);
        $this->nameConverter                        = $nameConverter;
    }

    /**
     * @param string $contentType
     * @param EncoderInterface $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, EncoderInterface $encoder, ?string $contentTypePattern=null) : void {
        $this->encoderFactory->addEncoder($contentType, $encoder, $contentTypePattern);
    }

    /**
     * @param mixed $object
     * @param string $contentType
     * @param array|null $nameConverter
     * @return string
     */
    public function serialize($object, string $contentType, array $nameConverter=null) : string {
        if ($encoder = $this->encoderFactory->getEncoder($contentType)) {
            $serializer                             = new Serializer(
                $encoder,
                $this->normalizer,
                $this->nameConverter
            );
            return $serializer->serialize($object, $nameConverter);
        } else {
            throw new RuntimeException("no encoder for contentType found, given ".$contentType);
        }
    }
}