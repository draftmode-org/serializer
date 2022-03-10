<?php
namespace Terrazza\Component\Serializer;
use ReflectionException;

interface DeserializerFactoryInterface {
    /**
     * @param string $contentType
     * @param DecoderInterface $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, DecoderInterface $decoder, ?string $contentTypePattern=null) : void;
    /**
     * @param class-string<T> $className
     * @param string $contentType
     * @param string|null $content
     * @param bool $restrictArguments
     * @return T|null
     * @throws ReflectionException
     * @template T of object
     */
    public function deserialize(string $className, string $contentType, ?string $content, bool $restrictArguments=false);
}