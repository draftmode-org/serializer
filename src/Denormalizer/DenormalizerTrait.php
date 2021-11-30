<?php
namespace Terrazza\Component\Serializer\Denormalizer;

use DateTime;
use Exception;
use InvalidArgumentException;
use Terrazza\Component\Serializer\TraceKeyTrait;

trait DenormalizerTrait {
    use TraceKeyTrait;
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