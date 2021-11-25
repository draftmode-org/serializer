<?php
namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface SerializerInterface {
    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @param bool $restrictUnInitialized
     * @param bool $restrictArguments
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input, bool $restrictUnInitialized=false, bool $restrictArguments=false);
}