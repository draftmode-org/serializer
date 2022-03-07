<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;
use Terrazza\Component\Serializer\INormalizer;

class Normalizer {
    public static function get($stream=null) : INormalizer {
        $logger = Logger::get($stream);
        return new \Terrazza\Component\Serializer\Normalizer($logger);
    }
}