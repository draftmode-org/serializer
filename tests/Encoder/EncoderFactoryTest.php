<?php

namespace Terrazza\Component\Serializer\Tests\Encoder;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Serializer\Encoder\EncoderFactory;
use Terrazza\Component\Serializer\Tests\_Mocks\Logger;

class EncoderFactoryTest extends TestCase {

    public function testNullNoEncoder() {
        $logger     = Logger::get();
        $factory    = new EncoderFactory($logger);
        $this->assertNull($factory->encode(null, "your"));
    }

    public function testNoEncoderFound() {
        $logger     = Logger::get();
        $factory    = new EncoderFactory($logger);
        $this->expectException(RuntimeException::class);
        $factory->encode([], "your");
    }
}