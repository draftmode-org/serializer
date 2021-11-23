<?php
namespace Terrazza\Component\Serializer\Denormalizer;

use DateTime;
use Exception;
use InvalidArgumentException;
use Terrazza\Component\Serializer\DenormalizerInterface;

trait DenormalizerTrait {
    private array $traceKey                         = [];

    /**
     * @param string $traceKey
     */
    private function pushTraceKey(string $traceKey) : void {
        array_push($this->traceKey, $traceKey);
    }

    private function popTraceKey() : void {
        array_pop($this->traceKey);
    }

    /**
     * @return string
     */
    private function getTraceKeys() : string {
        $response                                   = join(".",$this->traceKey);
        return strtr($response, [".[" => "["]);
    }

    private function getApprovedBuiltInValue_DateTime(string $inputValue) : DateTime {
        try {
            $result                                 = new DateTime($inputValue);
            $lastErrors                             = DateTime::getLastErrors();
            if ($lastErrors["warning_count"] || $lastErrors["error_count"]) {
                throw new InvalidArgumentException("argument ".$this->getTraceKeys()." value is not a valid date, given ".$inputValue);
            }
            return $result;
        } catch (Exception $exception) {
            throw new InvalidArgumentException("argument ".$this->getTraceKeys()." value is not a valid date, given ".$inputValue);
        }
    }
}