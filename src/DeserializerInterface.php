<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface DeserializerInterface {
    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize(string $className, $input, bool $restrictArguments=false);
}