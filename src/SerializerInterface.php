<?php
namespace Terrazza\Component\Serializer;

interface SerializerInterface {
    /**
     * @param object $object
     * @return mixed
     */
    public function serialize(object $object);
}