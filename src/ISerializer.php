<?php
namespace Terrazza\Component\Serializer;

interface ISerializer {
    /**
     * @param mixed $object
     * @param array|null $nameConverter
     * @return mixed
     */
    public function serialize($object, ?array $nameConverter=null);
}