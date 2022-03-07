<?php

namespace Terrazza\Component\Serializer\Tests\_Mocks;

use DateTime;
use Terrazza\Component\Serializer\IDeserializer;
use Terrazza\Component\Serializer\Factory\Json\JsonDeserializer;
use Terrazza\Component\Serializer\Factory\Json\JsonSerializer;
use Terrazza\Component\Serializer\ISerializer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentDateTime;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentUUID;

class JsonFactory {

    public static function getDeserializer($stream=null) : IDeserializer {
        $logger = Logger::get($stream);
        return new JsonDeserializer($logger);
    }

    public static function getSerializer($stream=null) : ISerializer {
        $logger = Logger::get($stream);
        return new JsonSerializer($logger, self::getNameConverter());
    }

    public static function getNameConverter() : array {
        return [
            SerializerRealLifeUUID::class           => SerializerRealLifePresentUUID::class,
            SerializerRealLifeProductAmount::class  => SerializerRealLifePresentAmount::class,
            SerializerRealLifeProductLabel::class   => SerializerRealLifePresentLabel::class,
            DateTime::class                         => SerializerRealLifePresentDateTime::class
        ];
    }
}