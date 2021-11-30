<?php
namespace Terrazza\Component\Serializer\Decoder;
use Terrazza\Component\Serializer\DecoderInterface;

class XMLDecoder implements DecoderInterface {
    private JsonDecoder $jsonDecoder;
    public function __construct() {
        $this->jsonDecoder                          = new JsonDecoder();
    }

    /**
     * @param mixed $data
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
        libxml_use_internal_errors(true);
        $xml                                        = simplexml_load_string($data);
        if ($xml === false) {
            foreach(libxml_get_errors() as $error) {
                throw new DecoderException("unable to convert xml, ".$error->message);
            }
        }
        $xmlJson                                    = json_encode($xml);
        return $this->jsonDecoder->decode($xmlJson);
    }
}