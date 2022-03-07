<?php
namespace Terrazza\Component\Serializer\Tests\_Mocks;

use Psr\Log\LoggerInterface;
use Terrazza\Component\Logger\Channel;
use Terrazza\Component\Logger\Converter\FormattedRecord\FormattedRecordFlat;
use Terrazza\Component\Logger\Converter\NonScalar\NonScalarJsonEncode;
use Terrazza\Component\Logger\Formatter\RecordFormatter;
use Terrazza\Component\Logger\Logger as rLogger;
use Terrazza\Component\Logger\Handler\SingleHandler;
use Terrazza\Component\Logger\LoggerFilter;
use Terrazza\Component\Logger\Utility\RecordValueConverter\RecordValueDate;
use Terrazza\Component\Logger\Utility\RecordValueConverter\RecordValueException;
use Terrazza\Component\Logger\Writer\StreamFile;

class Logger {
    public static function get($stream=null) : LoggerInterface {
        $logger                                     = new rLogger("Serializer");
        $format                                     = [
            "message" => "{Date} {Namespace}:{Method} (#{Line}) {Message} {Context}"
        ];
        if ($stream === true) {
            $stream                                 = "php://stdout";
        }
        if (is_string($stream)) {
            $formatter                              = new RecordFormatter(new NonScalarJsonEncode(), $format);
            $formatter->pushConverter("Date", new RecordValueDate());
            $formatter->pushConverter("Content.exception", new RecordValueException());
            $channel                                = new Channel("Main",
                new StreamFile(new FormattedRecordFlat(" "), $stream, FILE_APPEND),
                $formatter
            );
            $handler                                = new SingleHandler(rLogger::DEBUG, $channel);
            return $logger->withHandler($handler);
        } elseif ($stream === false) {
            return $logger;
        } else {
            return $logger;
        }
    }
}