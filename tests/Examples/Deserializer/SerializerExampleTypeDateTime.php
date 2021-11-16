<?php
namespace Terrazza\Component\Serializer\Tests\Examples\Deserializer;
use DateTime;

class SerializerExampleTypeDateTime {
    public DateTime $date;
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }
}