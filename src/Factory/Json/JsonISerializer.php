<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use Psr\Log\LoggerInterface;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\IEncoder;
use Terrazza\Component\Serializer\Normalizer\ArrayNormalizer;
use Terrazza\Component\Serializer\INormalizer;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\ISerializer;

class JsonISerializer implements ISerializer {
    private INormalizer $normalizer;
    private IEncoder $encoder;
    public function __construct(LoggerInterface $logger, array $nameConverter=null) {
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