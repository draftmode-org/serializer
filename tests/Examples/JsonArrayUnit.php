<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use DateTime;
use Terrazza\Component\Serializer\DeserializerInterface;
use Terrazza\Component\Serializer\Factory\Json\JsonDeserializer;
use Terrazza\Component\Serializer\Factory\Json\JsonSerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentLabel;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentUUID;

class JsonArrayUnit {

    public static function getDeserializer(bool $logLevel=false) : DeserializerInterface {
        return new JsonDeserializer(
            LoggerUnit::getLogger("Deserializer", $logLevel)
        );
    }

    public static function getSerializer(bool $logLevel=false) : SerializerInterface {
        return new JsonSerializer(
            LoggerUnit::getLogger("Serializer", $logLevel),
            [
                SerializerRealLifeUUID::class => SerializerRealLifePresentUUID::class,
                SerializerRealLifeProductAmount::class => SerializerRealLifePresentAmount::class,
                SerializerRealLifeProductLabel::class => SerializerRealLifePresentLabel::class,
                DateTime::class => SerializerRealLifePresentDateTime::class
            ]
        );
    }
}