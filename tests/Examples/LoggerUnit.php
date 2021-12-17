<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use Psr\Log\LoggerInterface;
use Terrazza\Component\Logger\Channel;
use Terrazza\Component\Logger\Formatter\ArrayFormatter;
use Terrazza\Component\Logger\Handler\HandlerPattern;
use Terrazza\Component\Logger\Handler\SingleHandler;
use Terrazza\Component\Logger\Logger;
use Terrazza\Component\Logger\Normalizer\NormalizeFlat;
use Terrazza\Component\Logger\Writer\StreamWriter;

class LoggerUnit {
    public static function getLogger(string $logName, bool $log) : LoggerInterface {
        if ($log) {
            $writer     = new StreamWriter("php://stdout");
            $formatter  = new ArrayFormatter("Y-m-d H:i:s.U", new NormalizeFlat(" "));
            $handler    = new SingleHandler(new HandlerPattern(Logger::DEBUG), new Channel("channelName", $writer, $formatter),
            ["Date", "Line" => "{Context.line}#%04d", "Message"]);
            file_put_contents("php://stdout", PHP_EOL);
            return new Logger($logName, [], $handler);
        } else {
            return new Logger($logName, []);
        }
    }
}