<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use Terrazza\Component\Serializer\DeserializerInterface;
use Terrazza\Component\Serializer\Factory\Json\JsonArrayDeserializer;
use Terrazza\Component\Serializer\Factory\Json\JsonArraySerializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonArrayUnit {

    public static function getDeserializer(bool $logLevel=false) : DeserializerInterface {
        return new JsonArrayDeserializer(
            LoggerUnit::getLogger("Deserializer", $logLevel)
        );
    }

    public static function getSerializer(bool $logLevel=false, ?array $nameConverter=null) : SerializerInterface {
        return new JsonArraySerializer(
            LoggerUnit::getLogger("Deserializer", $logLevel),
            $nameConverter
        );
    }
}