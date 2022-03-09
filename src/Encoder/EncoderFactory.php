<?php
namespace Terrazza\Component\Serializer\Encoder;

use Psr\Log\LoggerInterface;

class EncoderFactory {
    private LoggerInterface $logger;
    /**
     * @var array|IEncoder[]
     */
    private array $encoders;
    private array $contentTypePatterns;

    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
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
        $this->logger->debug("try to get encode for contentType:$contentType");
        $contentType                                = strtolower($contentType);
        if ($decoder = $this->encoders[$contentType] ?? null) {
            $this->logger->debug("encode found: contentType");
            return $decoder;
        }
        foreach ($this->contentTypePatterns as $useContentType => $pattern) {
            $this->logger->debug("try to get encoder in pattern $pattern");
            if (preg_match("#$pattern#", $contentType)) {
                $this->logger->debug("encoder found in pattern, use contentType $useContentType");
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