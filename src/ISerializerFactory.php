<?php
namespace Terrazza\Component\Serializer;
use ReflectionException;
use Terrazza\Component\Serializer\Encoder\IEncoder;

interface ISerializerFactory {
    /**
     * @param string $contentType
     * @param IEncoder $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, IEncoder $encoder, ?string $contentTypePattern=null) : void;
    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     * @throws ReflectionException
     */
    public function serialize($object, string $contentType, array $nameConverter=null);
}