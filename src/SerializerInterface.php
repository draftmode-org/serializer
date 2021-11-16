<?php
namespace Terrazza\Component\Serializer;

use InvalidArgumentException;
use ReflectionException;

interface SerializerInterface {
    /**
     * @param class-string<T>|object $className
     * @param mixed $input
     * @return T
     * @template T
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function deserialize($className, $input);

    /**
     * @param int $allowedAccess
     * @return SerializerInterface
     */
    public function withAllowedAccess(int $allowedAccess) : SerializerInterface;
}