<?php
namespace Terrazza\Component\Serializer;

use Terrazza\Component\Serializer\Encoder\IEncoder;

/**
 * 1. normalize an object into an array
 * 2. encode an array based on give IEncoder
 */
class Serializer implements ISerializer {
    use TraceKeyTrait;
    private IEncoder $encoder;
    private INormalizer $normalizer;

    /**
     * @param IEncoder $encoder
     * @param INormalizer $normalizer
     * @param array|null $nameConverter
     */
    public function __construct(IEncoder $encoder, INormalizer $normalizer, array $nameConverter=null) {
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