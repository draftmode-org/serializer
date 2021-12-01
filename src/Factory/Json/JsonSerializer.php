<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\EncoderInterface;
use Terrazza\Component\Serializer\Normalizer\ArrayNormalizer;
use Terrazza\Component\Serializer\NormalizerInterface;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonSerializer implements SerializerInterface {
    private NormalizerInterface $normalizer;
    private EncoderInterface $encoder;
    public function __construct(LogInterface $logger, array $nameConverter=null) {
        $this->encoder                              = new JsonEncoder();
        $this->normalizer                           = new ArrayNormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
        //
        // extend nameConverter
        //
        if ($nameConverter) {
            $this->normalizer                       = $this->normalizer->withNameConverter($nameConverter);
        }
    }

    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     */
    public function serialize($object, array $nameConverter=null) : string {
        return (new Serializer($this->encoder, $this->normalizer))
            ->serialize($object, $nameConverter);
    }
}