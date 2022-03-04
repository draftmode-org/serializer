<?php

namespace Terrazza\Component\Serializer\Factory\Json;

use Psr\Log\LoggerInterface;
use Terrazza\Component\Annotation\IAnnotationFactory;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\IEncoder;
use Terrazza\Component\Serializer\Normalizer\ArrayNormalizer;
use Terrazza\Component\Serializer\INormalizer;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\ISerializer;

class JsonSerializer implements ISerializer {
    private INormalizer $normalizer;
    private IEncoder $encoder;
    public function __construct(LoggerInterface $logger, IAnnotationFactory $annotationFactory, array $nameConverter=null) {
        $this->encoder                              = new JsonEncoder();
        $this->normalizer                           = new ArrayNormalizer(
            $logger,
            $annotationFactory
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