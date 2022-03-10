<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;
use Terrazza\Component\Serializer\NormalizerInterface;

class Normalizer {
    public static function get($stream=null) : NormalizerInterface {
        $logger = Logger::get($stream);
        return new \Terrazza\Component\Serializer\Normalizer($logger);
    }
}