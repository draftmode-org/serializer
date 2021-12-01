<?php
namespace Terrazza\Component\Serializer;

interface SerializerInterface {
    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     */
    public function serialize($object, ?array $nameConverter=null);
}