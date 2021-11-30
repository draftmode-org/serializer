<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use Terrazza\Component\Serializer\DeserializerInterface;
use Terrazza\Component\Serializer\Factory\Json\JsonDeserializer;
use Terrazza\Component\Serializer\Factory\Json\JsonSerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentLabel;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentUUID;

class JsonArrayUnit {

    public static function getDeserializer(bool $logLevel=false) : DeserializerInterface {
        return new JsonDeserializer(
            LoggerUnit::getLogger("Deserializer", $logLevel)
        );
    }

    public static function getSerializer(bool $logLevel=false, ?array $nameConverter=null) : SerializerInterface {
        return new JsonSerializer(
            LoggerUnit::getLogger("Deserializer", $logLevel),
            $nameConverter ?? [
                SerializerRealLifeUUID::class => SerializerRealLifePresentUUID::class,
                SerializerRealLifeProductAmount::class => SerializerRealLifePresentAmount::class,
                SerializerRealLifeProductLabel::class => SerializerRealLifePresentLabel::class
            ]
        );
    }
}