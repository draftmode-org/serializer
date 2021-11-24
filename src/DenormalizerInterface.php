<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface DenormalizerInterface {
    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @param bool $isInitialized
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function denormalize($className, $input, bool $isInitialized=true) : object;
}