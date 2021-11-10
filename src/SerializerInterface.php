<?php
namespace Terrazza\Component\Serializer;

interface SerializerInterface {
    /**
     * Deserializes data into the given type.
     * @param class-string<T>|object $className
     * @template T of object
     * @param mixed $input
     * @return mixed
     */
    public function deserialize($className, $input);
}