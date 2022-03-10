<?php

namespace Terrazza\Component\Serializer\Tests\Encoder;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Serializer\Encoder\EncoderFactory;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\Tests\_Mocks\Logger;

class EncoderFactoryTest extends TestCase {

    public function testNullNoEncoder() {
        $logger     = Logger::get();
        $factory    = new EncoderFactory($logger);
        $this->assertEquals("null", $factory->encode("json", null));
    }

    public function testGetAddEncoder() {
        $logger     = Logger::get();
        $factory    = new EncoderFactory($logger);
        $factory->addEncoder("my", $encoder = new JsonEncoder(), $pattern = "myPattern");
        $this->assertEquals([
            null,
            $encoder,
        ],[
            $factory->getEncoder("unknown"),
            $factory->getEncoder($pattern)
        ]);
    }

    public function testNoEncoderFound() {
        $logger     = Logger::get();
        $factory    = new EncoderFactory($logger);
        $this->expectException(RuntimeException::class);
        $factory->encode("your", []);
    }
}