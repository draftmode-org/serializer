<?php

namespace Terrazza\Component\Serializer\Tests\Examples;

use Terrazza\Component\Logger\Formatter\LineFormatter;
use Terrazza\Component\Logger\Handler\NoHandler;
use Terrazza\Component\Logger\Handler\StreamHandler;
use Terrazza\Component\Logger\Log;
use Terrazza\Component\Logger\LogInterface;

class LoggerUnit {
    public static function getLogger(string $logName, bool $log) : LogInterface {
        $handler = $log ?
            new StreamHandler(
                Log::DEBUG,
                new LineFormatter(),
                "php://stdout"
            ) : new NoHandler();
        if ($log) {
            file_put_contents("php://stdout", PHP_EOL);
        }
        return new Log($logName, $handler);
    }
}