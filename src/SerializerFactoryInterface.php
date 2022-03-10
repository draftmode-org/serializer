<?php
namespace Terrazza\Component\Serializer;
use ReflectionException;

interface SerializerFactoryInterface {
    /**
     * @param string $contentType
     * @param EncoderInterface $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, EncoderInterface $encoder, string $contentTypePattern=null) : void;

    /**
     * @param mixed $object
     * @param string $contentType
     * @param array|null $nameConverter
     * @return string
     * @throws ReflectionException
     */
    public function serialize($object, string $contentType, array $nameConverter=null) : string;
}