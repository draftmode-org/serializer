<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;
use Terrazza\Component\ReflectionClass\ClassName\ReflectionClassClassNameException;

interface DenormalizerInterface {
    /**
     * @param object|string $class
     * @param mixed $input
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws ReflectionClassClassNameException
     * @return object
     */
    public function denormalize($class, $input) : object;
}