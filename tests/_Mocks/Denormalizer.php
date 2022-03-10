<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;
use Terrazza\Component\Serializer\DenormalizerInterface;

class Denormalizer {
    public static function get($stream=null) : DenormalizerInterface {
        $logger = Logger::get($stream);
        return new \Terrazza\Component\Serializer\Denormalizer($logger);
    }
}