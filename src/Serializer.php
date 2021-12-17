<?php

namespace Terrazza\Component\Serializer;

class Serializer implements ISerializer {
    use TraceKeyTrait;
    private IEncoder $encoder;
    private INormalizer $normalizer;

    /**
     * @param IEncoder $encoder
     * @param INormalizer $normalizer
     */
    public function __construct(IEncoder $encoder, INormalizer $normalizer) {
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