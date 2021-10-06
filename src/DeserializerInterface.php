<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface DeserializerInterface {
    /**
     * @param object|string $instance
     * @param mixed $input
     * @return object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($instance, $input) : object;
}