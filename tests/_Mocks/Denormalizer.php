<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;

use Terrazza\Component\Serializer\IDenormalizer;

class Denormalizer {
    public static function get($stream=null) : IDenormalizer {
        $logger = Logger::get($stream);
        return new \Terrazza\Component\Serializer\Denormalizer($logger);
    }
}