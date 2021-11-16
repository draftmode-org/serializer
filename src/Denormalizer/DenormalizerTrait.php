<?php

namespace Terrazza\Component\Serializer\Denormalizer;

use DateTime;
use Exception;
use InvalidArgumentException;

trait DenormalizerTrait {
    private array $traceKey                         = [];
    private array $builtIn                          = ["int", "integer", "float", "double", "string", "DateTime"];

    /**
     * @param string $type
     * @return bool
     */
    private function isBuiltIn(string $type) : bool {
        return in_array($type, $this->builtIn);
    }

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

    /**
     * @param string $methodOrProperty
     * @param bool $public
     * @param bool $protected
     * @param bool $private
     */
    private function isAllowed(string $methodOrProperty, bool $public, bool $protected, bool $private) : void {
        if ($protected && $this->allowedAccess < 1) {
            throw new InvalidArgumentException($methodOrProperty." cannot be accessed, is protected");
        }
        if ($private && $this->allowedAccess < 2) {
            throw new InvalidArgumentException($methodOrProperty." cannot be accessed, is private");
        }
    }

    /**
     * @param string|null $parameterType
     * @param mixed $inputValue
     * @return mixed
     */
    private function getApprovedBuiltInValue(?string $parameterType, $inputValue) {
        if ($parameterType) {
            $inputType                              = gettype($inputValue);
            $inputType                              = strtr($inputType, [
                "integer"                           => "int",
                "double"                            => "float"
            ]);
            if ($parameterType === $inputType) {
                return $inputValue;
            } elseif ($parameterType == "string" && ($inputType === "int" || $inputType === "float")) {
                return $inputValue;
            } elseif ($parameterType == "float" && $inputType === "int") {
                return $inputValue;
            }
            else {
                $user_callback                      = [$this, "getApprovedBuiltInValue_".$parameterType];
                if (is_callable($user_callback, false, $callback)) {
                    return call_user_func($user_callback, $inputValue);
                }
            }
            throw new InvalidArgumentException("argument ".$this->getTraceKeys()." expected type ".$parameterType.", given ".$inputType);
        } else {
            return $inputValue;
        }
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