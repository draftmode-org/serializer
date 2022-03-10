<?php
namespace Terrazza\Component\Serializer\Decoder;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Terrazza\Component\Serializer\DecoderInterface;

class DecoderFactory {
    private LoggerInterface $logger;
    /**
     * @var array|DecoderInterface[]
     */
    private array $decoders;
    private array $contentTypePatterns;

    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
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
     * @param DecoderInterface $decoder
     * @param string|null $contentTypePattern
     */
    public function addDecoder(string $contentType, DecoderInterface $decoder, ?string $contentTypePattern=null) : void {
        $contentType                                = strtolower($contentType);
        $this->decoders[$contentType]               = $decoder;
        if ($contentTypePattern) {
            $this->contentTypePatterns[$contentType]= strtolower($contentTypePattern);
        }
    }

    /**
     * @param string $contentType
     * @return DecoderInterface|null
     */
    public function getDecoder(string $contentType) :?DecoderInterface {
        $this->logger->debug("get decoder for contentType:$contentType", ["patterns" => $this->contentTypePatterns]);
        $contentType                                = strtolower($contentType);
        if ($decoder = $this->decoders[$contentType] ?? null) {
            $this->logger->debug("decoder found for contentType $contentType");
            return $decoder;
        }
        foreach ($this->contentTypePatterns as $useContentType => $pattern) {
            $this->logger->debug("find decoder with preg match $pattern");
            if (preg_match("#$pattern#", $contentType)) {
                $this->logger->debug("decoder found in pattern, use contentType $useContentType");
                return $this->decoders[$useContentType] ?? null;
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
        }
        throw new RuntimeException("no decoder for contentType found, given ".$contentType);
    }
}