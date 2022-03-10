<?php
namespace Terrazza\Component\Serializer\Encoder;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Terrazza\Component\Serializer\EncoderInterface;

class EncoderFactory {
    private LoggerInterface $logger;
    /**
     * @var array|EncoderInterface[]
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
     * @param EncoderInterface $encoder
     * @param string|null $contentTypePattern
     */
    public function addEncoder(string $contentType, EncoderInterface $encoder, string $contentTypePattern=null) : void {
        $contentType                                = strtolower($contentType);
        $this->encoders[$contentType]               = $encoder;
        if ($contentTypePattern) {
            $this->contentTypePatterns[$contentType]= strtolower($contentTypePattern);
        }
    }

    /**
     * @param string $contentType
     * @return EncoderInterface|null
     */
    public function getEncoder(string $contentType) :?EncoderInterface {
        $this->logger->debug("get encode for contentType:$contentType", ["patterns" => $this->contentTypePatterns]);
        $contentType                                = strtolower($contentType);
        if ($decoder = $this->encoders[$contentType] ?? null) {
            $this->logger->debug("encode found for contentType $contentType");
            return $decoder;
        }
        foreach ($this->contentTypePatterns as $useContentType => $pattern) {
            $this->logger->debug("find encoder with preg match $pattern");
            if ($found = preg_match("#$pattern#", $contentType)) {
                $this->logger->debug("encoder found for pattern, use contentType $useContentType");
                return $this->encoders[$useContentType] ?? null;
            } else {
                $this->logger->debug("...found", ["found" => $found]);
            }
        }
        return null;
    }

    /**
     * @param string $contentType
     * @param array|null $data
     * @return string
     */
    public function encode(string $contentType, ?array $data) : string {
        if ($encoder = $this->getEncoder($contentType)) {
            return $encoder->encode($data);
        }
        throw new RuntimeException("no encoder for contentType found, given ".$contentType);
    }
}