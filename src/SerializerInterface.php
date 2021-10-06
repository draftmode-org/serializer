<?php
namespace Terrazza\Component\Serializer;

interface SerializerInterface {
    /**
     * Deserializes data into the given type.
     * @param string $className
     * @param mixed $input
     * @return mixed
     */
    public function deserialize(string $className, $input);
}