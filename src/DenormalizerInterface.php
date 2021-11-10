<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface DenormalizerInterface {
    /**
     * @param object|string $class
     * @param mixed $input
     * @return object
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function denormalize($class, $input) : object;
}