<?php

namespace Terrazza\Component\Serializer\Tests\Decoder;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Decoder\Exception\DecoderException;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;

class JsonDecoderTest extends TestCase {
    function testSuccessful() {
        $json = json_encode(
            $value = array(
                1 => array(
                    'English' => array(
                        'One',
                        'January'
                    ),
                    'French' => array(
                        'Une',
                        'Janvier'
                    )
                )
            )
        );
        $decoded = (new JsonDecoder())->decode($json);
        $this->assertEquals($value, $decoded);
    }

    function testNull() {
        $this->assertNull((new JsonDecoder())->decode(null, true));
    }

    function testFailureDepth() {
        $json = json_encode(
            array(
                1 => array(
                    'English' => array(
                        'One',
                        'January'
                    ),
                    'French' => array(
                        'Une',
                        'Janvier'
                    )
                )
            )
        );
        $this->expectException(DecoderException::class);
        (new JsonDecoder(1))->decode($json);
    }

    function testFailureNull() {
        $this->assertNull((new JsonDecoder())->decode(null));
    }
}