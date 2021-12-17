<?php

namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface IDeserializer {
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
    public function deserialize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false);
}