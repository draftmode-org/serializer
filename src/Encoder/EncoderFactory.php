<?php
namespace Terrazza\Component\Serializer\Encoder;

class EncoderFactory {
    /**
     * @var array|IEncoder[]
     */
    private array $encoders;
    private array $contentTypePatterns;

    public function __construct() {
        $this->encoders                             = [
            "json"                                  => new JsonEncoder()
        ];
        $this->contentTypePatterns                  = [
            "json"                                  => "application/json",
            "xml"                                   => "(application/xml|text/xml)",
        ];
    }

    /**
     * @param string $contentType
     * @param IEncoder $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, IEncoder $encoder, ?string $contentTypePattern) : void {
        $contentType                                = strtolower($contentType);
        $this->encoders[$contentType]               = $encoder;
        if ($contentTypePattern) {
            $this->contentTypePatterns[$contentType]= $contentTypePattern;
        }
    }

    /**
     * @param string $contentType
     * @return IEncoder|null
     */
    private function getEncoder(string $contentType) :?IEncoder {
        $contentType                                = strtolower($contentType);
        if ($decoder = $this->encoders[$contentType] ?? null) {
            return $decoder;
        }
        foreach ($this->contentTypePatterns as $useContentType => $pattern) {
            if (preg_match("#$pattern#", $contentType)) {
                return $this->decoders[$contentType] ?? null;
            }
        }
        return null;
    }
    /**
     * @param array $data
     * @param string $contentType
     * @return mixed
     */
    public function encode(array $data, string $contentType) {
        if ($encoder = $this->getEncoder($contentType)) {
            return $encoder->encode($data);
        }
        return null;
    }
}