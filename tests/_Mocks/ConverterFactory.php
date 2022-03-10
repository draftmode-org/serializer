<?php

namespace Terrazza\Component\Serializer\Tests\_Mocks;

use DateTime;
use Terrazza\Component\Serializer\Factory\SerializerFactory;
use Terrazza\Component\Serializer\Factory\DeserializerFactory;
use Terrazza\Component\Serializer\SerializerFactoryInterface;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentDateTime;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentUUID;
use Terrazza\Component\Serializer\DeserializerFactoryInterface;

class ConverterFactory {

    public static function getDeserializer($stream=null) : DeserializerFactoryInterface {
        $logger = Logger::get($stream);
        return new DeserializerFactory($logger);
    }

    public static function getSerializer($stream=null) : SerializerFactoryInterface {
        $logger = Logger::get($stream);
        return new SerializerFactory($logger, self::getNameConverter());
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