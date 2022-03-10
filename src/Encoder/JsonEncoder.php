<?php
namespace Terrazza\Component\Serializer\Encoder;
use Terrazza\Component\Serializer\EncoderInterface;

class JsonEncoder implements EncoderInterface {
    private int $encodeFlags;
    private int $encodeDepth;
    public function __construct(int $encodeFlags = 0, int $encodeDepth = 512) {
        $this->encodeFlags                          = $encodeFlags;
        $this->encodeDepth                          = $encodeDepth;
    }

    /**
     * @param array|null $data
     * @return string
     */
    function encode(?array $data) : string {
        return json_encode($data, $this->encodeFlags, $this->encodeDepth);
    }
}