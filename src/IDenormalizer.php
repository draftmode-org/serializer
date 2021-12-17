<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface IDenormalizer {
    /**
     * @param class-string<T>|T $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function denormalize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false) : object;
}