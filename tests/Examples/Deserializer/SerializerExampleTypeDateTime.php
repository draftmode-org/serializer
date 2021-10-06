<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;
use DateTime;

class SerializerExampleTypeDateTime {
    public DateTime $date;
    public function __construct(DateTime $date) {
        $this->date = $date;
    }
}