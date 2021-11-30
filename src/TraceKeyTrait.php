<?php
namespace Terrazza\Component\Serializer;

trait TraceKeyTrait {
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
}