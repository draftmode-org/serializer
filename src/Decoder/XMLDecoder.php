<?php
namespace Terrazza\Component\Serializer\Decoder;
use Terrazza\Component\Serializer\Decoder\Exception\DecoderException;
use Terrazza\Component\Serializer\DecoderInterface;

class XMLDecoder implements DecoderInterface {
    private JsonDecoder $jsonDecoder;
    public function __construct() {
        $this->jsonDecoder                          = new JsonDecoder();
    }

    /**
     * @param mixed $data
     * @return array|null
     * @throws DecoderException
     */
    function decode($data) : ?array {
        if (is_null($data)) {
            return null;
        }
        libxml_use_internal_errors(true);
        $xml                                        = simplexml_load_string($data);
        if ($xml === false) {
            foreach(libxml_get_errors() as $error) {
                throw new DecoderException("unable to convert xml: ".$error->message);
            }
        }
        $xmlJson                                    = json_encode($xml);
        return $this->jsonDecoder->decode($xmlJson);
    }
}