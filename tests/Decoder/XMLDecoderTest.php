<?php

namespace Terrazza\Component\Serializer\Tests\Decoder;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Decoder\DecoderException;
use Terrazza\Component\Serializer\Decoder\XMLDecoder;

class XMLDecoderTest extends TestCase
{

    function testSuccessfulWithoutArray() {
        $node1 = "to";
        $node2 = "Do not forget me this weekend!";
        $image1 = "image1";
        $note=<<<XML
<note>
<node1>{$node1}</node1>
<node2>{$node2}</node2>
<images>
    <image>{$image1}</image>
</images>
</note>
XML;
        $decoded = (new XMLDecoder())->decode($note);
        $this->assertEquals([
            "node1" => $node1,
            "node2" => $node2,
            "images" => [
                "image" => $image1
            ]
        ], $decoded);
    }

    function testSuccessfulWithArray() {
        $node1 = "to";
        $node2 = "Do not forget me this weekend!";
        $image1 = "image1";
        $image2 = "image2";
        $note=<<<XML
<note>
<node1>{$node1}</node1>
<node2>{$node2}</node2>
<images>
    <image>{$image1}</image>
    <image>{$image2}</image>
</images>
</note>
XML;
        $decoded = (new XMLDecoder())->decode($note);
        $this->assertEquals([
            "node1" => $node1,
            "node2" => $node2,
            "images" => [
                "image" => [
                    $image1,
                    $image2,
                ]
            ]
        ], $decoded);
    }

    function testNull() {
        $this->assertNull((new XMLDecoder())->decode(null, true));
    }

    function testFailureClosedTag() {
        $note=<<<XML
<note>
<to>Tove</to>
<from>Jani</from>
<heading>Reminder</heading>
<body>Do not forget me this weekend!<body>
</note>
XML;
        $this->expectException(DecoderException::class);
        (new XMLDecoder(1))->decode($note);
    }

    function testFailureNull() {
        $this->expectException(DecoderException::class);
        $this->assertNull((new XMLDecoder())->decode(null));
    }
}