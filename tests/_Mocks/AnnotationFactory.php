<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;

use Psr\Log\LoggerInterface;
use Terrazza\Component\Annotation\IAnnotationFactory;

class AnnotationFactory {
    public static function get(LoggerInterface $logger) : IAnnotationFactory {
        return new \Terrazza\Component\Annotation\AnnotationFactory(
            $logger
        );
    }
}