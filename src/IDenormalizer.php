<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

interface IDenormalizer {
    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @param bool $restrictArguments
     * @return T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @template T of object
     */
    public function denormalizeClass(string $className, $input, bool $restrictArguments=false);

    /**
     * @param object $object
     * @param string $methodName
     * @param mixed $input
     * @param bool $restrictArguments
     * @return mixed
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalizeMethodValues(object $object, string $methodName, $input, bool $restrictArguments=false);
}