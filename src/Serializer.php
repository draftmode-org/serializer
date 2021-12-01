<?php

namespace Terrazza\Component\Serializer;

class Serializer implements SerializerInterface {
    use TraceKeyTrait;
    private EncoderInterface $encoder;
    private NormalizerInterface $normalizer;

    /**
     * @param EncoderInterface $encoder
     * @param NormalizerInterface $normalizer
     */
    public function __construct(EncoderInterface $encoder, NormalizerInterface $normalizer) {
        $this->encoder                              = $encoder;
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