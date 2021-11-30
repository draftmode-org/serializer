<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use ReflectionException;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\EncoderInterface;
use Terrazza\Component\Serializer\Normalizer;
use Terrazza\Component\Serializer\NormalizerInterface;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonArraySerializer implements SerializerInterface {
    private NormalizerInterface $normalizer;
    private EncoderInterface $encoder;
    public function __construct(LogInterface $logger, array $nameConverter=null) {
        $this->encoder                              = new JsonEncoder();
        $this->normalizer                           = new Normalizer(
            $logger,
            new AnnotationFactory(
                new ClassNameResolver()
            ),
            $nameConverter
        );
    }

    /**
     * @param object $object
     * @return string
     * @throws ReflectionException
     */
    public function serialize(object $object) : string {
        return $this->encoder->encode(
            $this->normalizer->normalize($object)
        );
    }
}