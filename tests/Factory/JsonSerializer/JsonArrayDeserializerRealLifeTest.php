<?php

namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Logger\Formatter\LineFormatter;
use Terrazza\Component\Logger\Handler\NoHandler;
use Terrazza\Component\Logger\Handler\StreamHandler;
use Terrazza\Component\Logger\Log;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Factory\Json\JsonArraySerializer;
use Terrazza\Component\Serializer\SerializerInterface;

class JsonArrayDeserializerRealLifeTest extends TestCase {
    protected function getLogger(?bool $log=null) : LogInterface {
        $handler = $log ?
            new StreamHandler(
                $logLevel ?? Log::DEBUG,
                new LineFormatter(),
                "php://stdout"
            ) : new NoHandler();
        if ($log) {
            file_put_contents("php://stdout", PHP_EOL);
        }
        return new Log("Serializer", $handler);
    }

    private function getSerializer(int $logLevel=null) : SerializerInterface {
        return new JsonArraySerializer(
            $this->getLogger($logLevel)
        );
    }

    function test() {
        $product = new JsonArrayDeserializerRealLifeProduct(
            new JsonArrayDeserializerRealLifeUUID($id = "221"),
        );
        $update = clone $product;
        $update->getPrice()->setRegular(
            new JsonArrayDeserializerRealLifeAmount($updatePriceRegular = 13.12)
        );
        //$product->setPrice(null);
        $this->assertEquals([
            $product->getId()->getValue(),
            $product->getPrice()->getRegular()->getValue(),
            $product->getPrice()->getOffer()->getValue(),

            $update->getPrice()->getRegular()->getValue()

        ],[
            $id,
            $updatePriceRegular, //null,
            null,

            $updatePriceRegular
        ]);
    }
}

class JsonArrayDeserializerRealLifeProduct {
    private JsonArrayDeserializerRealLifeUUID $id;
    private JsonArrayDeserializerRealLifePrice $price;

    /**
     * @param JsonArrayDeserializerRealLifeUUID $id
     */
    public function __construct(JsonArrayDeserializerRealLifeUUID $id)
    {
        $this->id = $id;
        $this->price = new JsonArrayDeserializerRealLifePrice;
    }

    /**
     * @param JsonArrayDeserializerRealLifePrice|null $price
     */
    public function setPrice(?JsonArrayDeserializerRealLifePrice $price): void
    {
        $this->price = $price ?: new JsonArrayDeserializerRealLifePrice;
    }

    /**
     * @return JsonArrayDeserializerRealLifeUUID
     */
    public function getId(): JsonArrayDeserializerRealLifeUUID
    {
        return $this->id;
    }

    /**
     * @return JsonArrayDeserializerRealLifePrice
     */
    public function getPrice(): JsonArrayDeserializerRealLifePrice
    {
        return $this->price;
    }


}
class JsonArrayDeserializerRealLifeUUID {
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

}
class JsonArrayDeserializerRealLifeAmount {
    private ?float $value;

    /**
     * @param float|null $value
     */
    public function __construct(float $value=null)
    {
        $this->value = $value;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

}
class JsonArrayDeserializerRealLifePrice {
    private ?JsonArrayDeserializerRealLifeAmount $regular;
    private ?JsonArrayDeserializerRealLifeAmount $offer;

    public function __construct() {
        $this->regular = new JsonArrayDeserializerRealLifeAmount;
        $this->offer = new JsonArrayDeserializerRealLifeAmount;
    }

    /**
     * @return JsonArrayDeserializerRealLifeAmount|null
     */
    public function getRegular(): ?JsonArrayDeserializerRealLifeAmount
    {
        return $this->regular;
    }

    /**
     * @param JsonArrayDeserializerRealLifeAmount|null $regular
     * @return JsonArrayDeserializerRealLifePrice
     */
    public function setRegular(?JsonArrayDeserializerRealLifeAmount $regular): self {
        $this->regular = $regular;
        return $this;
    }

    /**
     * @return JsonArrayDeserializerRealLifeAmount|null
     */
    public function getOffer(): ?JsonArrayDeserializerRealLifeAmount
    {
        return $this->offer;
    }

    /**
     * @param JsonArrayDeserializerRealLifeAmount|null $offer
     */
    public function setOffer(?JsonArrayDeserializerRealLifeAmount $offer): void
    {
        $this->offer = $offer;
    }


}
