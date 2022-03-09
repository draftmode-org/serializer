<?php
namespace Terrazza\Component\Serializer;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Terrazza\Component\Serializer\Encoder\EncoderFactory;
use Terrazza\Component\Serializer\Encoder\IEncoder;

/**
 * 1. normalize an object into an array
 * 2. encode an array into json/xml/...
 */
class SerializerFactory implements ISerializerFactory {
    private EncoderFactory $encoderFactory;
    private INormalizer $normalizer;
    public function __construct(LoggerInterface $logger, array $nameConverter=null) {
        $this->encoderFactory                       = new EncoderFactory();
        $normalizer                                 = new Normalizer($logger);
        if ($nameConverter) {
            $normalizer                             = $normalizer->withNameConverter($nameConverter);
        }
        $this->normalizer                           = $normalizer;
    }

    /**
     * @param string $contentType
     * @param IEncoder $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, IEncoder $encoder, ?string $contentTypePattern=null) : void {
        $this->encoderFactory->addEncoder($contentType, $encoder, $contentTypePattern);
    }

    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     * @throws ReflectionException
     */
    public function serialize($object, string $contentType, array $nameConverter=null) {
        if ($nameConverter) {
            $normalizer                             = $this->normalizer->withNameConverter($nameConverter);
        } else {
            $normalizer                             = $this->normalizer;
        }
        $data                                       = $normalizer->normalize($object);
        return $this->encoderFactory->encode($data, $contentType);
    }
}