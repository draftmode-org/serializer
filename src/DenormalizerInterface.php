<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

interface DenormalizerInterface {
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
     * @param ReflectionMethod $method
     * @param mixed $input
     * @param bool $restrictArguments
     * @return mixed
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function denormalizeMethod(ReflectionMethod $method, $input, bool $restrictArguments=false);
}