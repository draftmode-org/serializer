<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;
use RuntimeException;

interface IDenormalizer {
    /**
     * @param class-string<T> $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalize(string $className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) : object;

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