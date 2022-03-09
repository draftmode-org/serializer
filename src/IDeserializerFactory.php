<?php
namespace Terrazza\Component\Serializer;
use ReflectionException;
use Terrazza\Component\Serializer\Decoder\IDecoder;

interface IDeserializerFactory {
    /**
     * @param string $contentType
     * @param IDecoder $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, IDecoder $decoder, ?string $contentTypePattern=null) : void;
    /**
     * @param class-string<T> $className
     * @param mixed $content
     * @param string $contentType
     * @param bool $restrictArguments
     * @return T|null
     * @throws ReflectionException
     * @template T of object
     */
    public function deserialize(string $className, $content, string $contentType, bool $restrictArguments=false);
}