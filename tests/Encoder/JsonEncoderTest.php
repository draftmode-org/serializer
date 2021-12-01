<?php
namespace Terrazza\Component\Serializer\Tests\Encoder;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;

class JsonEncoderTest extends TestCase {

    function testEncoder() {
        $data = ["key" => "value"];
        $this->assertEquals(json_encode($data), ((new JsonEncoder)->encode($data)));
    }

}