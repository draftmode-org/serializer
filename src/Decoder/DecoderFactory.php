<?php
namespace Terrazza\Component\Serializer\Decoder;
use RuntimeException;

class DecoderFactory {
    /**
     * @var array|IDecoder[]
     */
    private array $decoders;
    private array $contentTypePatterns;

    public function __construct() {
        $this->decoders                             = [
            "json"                                  => new JsonDecoder(),
            "xml"                                   => new XMLDecoder()
        ];
        $this->contentTypePatterns                  = [
            "json"                                  => "application/json",
            "xml"                                   => "(application/xml|text/xml)",
        ];
    }

    /**
     * @param string $contentType
     * @param IDecoder $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, IDecoder $decoder, ?string $contentTypePattern=null) : void {
        $contentType                                = strtolower($contentType);
        $this->decoders[$contentType]               = $decoder;
        if ($contentTypePattern) {
            $this->contentTypePatterns[$contentType]= $contentTypePattern;
        }
    }

    /**
     * @param string $contentType
     * @return IDecoder|null
     */
    private function getDecoder(string $contentType) :?IDecoder {
        $contentType                                = strtolower($contentType);
        if ($decoder = $this->decoders[$contentType] ?? null) {
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
     * @param mixed $data
     * @param string $contentType
     * @return array|null
     */
    public function decode($data, string $contentType) :?array {
        if (is_null($data)) {
            return null;
        }
        if ($decoder = $this->getDecoder($contentType)) {
            return $decoder->decode($data);
        } else {
            throw new RuntimeException("no decoder for inputType found, given ".$contentType);
        }
    }
}