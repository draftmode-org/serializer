<?php
namespace Terrazza\Component\Serializer\Decoder;
use Terrazza\Component\Serializer\DecoderInterface;

class JsonDecoder implements DecoderInterface {
    private int $decodeDepth;
    private int $decodeFlags;
    public function __construct(int $decodeDepth=512, int $decodeFlags = 0) {
        $this->decodeDepth                          = $decodeDepth;
        $this->decodeFlags                          = $decodeFlags;
    }

    /**
     * @param $data
     * @param bool $nullable
     * @return array|null
     */
    function decode($data, bool $nullable=false) : ?array {
        if (is_null($data)) {
            if ($nullable) {
                return null;
            } else {
                throw new DecoderException("data is not allowed to be null");
            }
        }
        $response                                   = json_decode($data, true, $this->decodeDepth, $this->decodeFlags);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DecoderException(json_last_error_msg());
        }
        return $response;
    }
}