<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;
use DateTime;
use Psr\Log\LoggerInterface;

class LoggerMock implements LoggerInterface {
    private ?string $stream;

    public static function get($stream=null) : LoggerInterface {
        if (is_string($stream)) {
            return new self($stream);
        } elseif (is_bool($stream)) {
            return new self($stream ? "php://stdout" : null);
        } else {
            return new self();
        }
    }

    public function __construct(?string $stream=null) {
        $this->stream = $stream;
        if ($stream) {
            file_put_contents($stream, PHP_EOL);
        }
    }

    private function addMessage($message, array $context=[]) : void {
        if ($this->stream !== null) {
            $messages                               = [];
            $message                                = (new DateTime())->format("Y-m-d H:i:s.u")." ".$message;
            $cKey                                   = "line";
            if (is_array($context) && array_key_exists($cKey, $context)) {
                $message                            .= "[#".$context[$cKey]."]";
                unset($context[$cKey]);
            }
            $messages[]                             = $message;
            if ($context && count($context)) {
                $messages[]                         = json_encode($context);
            }
            file_put_contents($this->stream, join(PHP_EOL, $messages).PHP_EOL);
        }
    }

    public function emergency($message, array $context = array()) { $this->addMessage($message, $context);}
    public function alert($message, array $context = array()) { $this->addMessage($message, $context);}
    public function critical($message, array $context = array()) { $this->addMessage($message, $context);}
    public function error($message, array $context = array()) { $this->addMessage($message, $context);}
    public function warning($message, array $context = array()) { $this->addMessage($message, $context);}
    public function notice($message, array $context = array()) { $this->addMessage($message, $context);}
    public function info($message, array $context = array()) { $this->addMessage($message, $context);}
    public function debug($message, array $context = array()) { $this->addMessage($message, $context);}
    public function log($level, $message, array $context = array()) { $this->addMessage($message, $context);}
}