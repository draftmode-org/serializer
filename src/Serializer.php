<?php
namespace Terrazza\Component\Serializer;

/**
 * 1. normalize an object into an array
 * 2. encode an array based on give IEncoder
 */
class Serializer implements SerializerInterface {
    use TraceKeyTrait;
    private EncoderInterface $encoder;
    private NormalizerInterface $normalizer;

    /**
     * @param EncoderInterface $encoder
     * @param NormalizerInterface $normalizer
     * @param array|null $nameConverter
     */
    public function __construct(EncoderInterface $encoder, NormalizerInterface $normalizer, array $nameConverter=null) {
        $this->encoder                              = $encoder;
        if ($nameConverter) {
            $normalizer                             = $normalizer->withNameConverter($nameConverter);
        }
        $this->normalizer                           = $normalizer;
    }

    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     */
    public function serialize($object, array $nameConverter=null) {
        if ($nameConverter) {
            $normalizer                             = $this->normalizer->withNameConverter($nameConverter);
        } else {
            $normalizer                             = $this->normalizer;
        }
        $data                                       = $normalizer->normalize($object);
        return $this->encoder->encode($data);
    }
}