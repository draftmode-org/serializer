<?php

namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Terrazza\Component\Logger\Formatter\LineFormatter;
use Terrazza\Component\Logger\Handler\NoHandler;
use Terrazza\Component\Logger\Handler\StreamHandler;
use Terrazza\Component\Logger\Log;
use Terrazza\Component\Logger\LogInterface;
use Terrazza\Component\Serializer\Factory\Json\JsonArraySerializer;
use Terrazza\Component\Serializer\SerializerInterface;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductUUID;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUserUUID;

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

    /**
     * @throws ReflectionException
     */
    function test() {
        $mProduct = new SerializerRealLifeProduct(
            new SerializerRealLifeProductUUID($id = "221"),
        );
        $mProduct->setDescription($mDescription = "mDescription");
        $mProduct->setUser(
            new SerializerRealLifeUserUUID($mUser = 12)
        );
        $mProduct->getPrice()->setRegular(
            new SerializerRealLifeProductAmount($mPriceRegular = 13.12)
        );
        //
        $serializer = $this->getSerializer();
        //
        // create with serializer
        //
        $sProduct   = $serializer->deserialize(SerializerRealLifeProduct::class, json_encode([
            'id'            => $id,
            'user'          => $sUser = "sUser",
            'description'   => $sDescription = "sDescription",
            'price' => [
                'regular'   => $mPriceRegular,
                'offer'     => $sPriceOffer = 12.0
            ]
        ]));
        //
        // update with serializer
        //
        /** @var SerializerRealLifeProduct $uProduct */
        $uProduct   = $serializer->deserialize($mProduct, json_encode([
            'user'          => $uUser = "uUser",
            'price' => [
                'offer'     => $uPriceOffer = 12.1
            ]
        ]));
        /** @var SerializerRealLifeProduct $u2Product */
        $u2Product  = $serializer->deserialize($mProduct, json_encode([
            'description'   => $u2Description = null
        ]));

        $this->assertEquals([
            $mProduct->getId()->getValue(),
            $mProduct->getPrice()->getRegular()->getValue(),
            $mProduct->getPrice()->getOffer()->getValue(),
            $mProduct->getUser()->getValue(),
            $mProduct->getDescription(),

            $sProduct->getId()->getValue(),
            $sProduct->getPrice()->getRegular()->getValue(),
            $sProduct->getPrice()->getOffer()->getValue(),
            $sProduct->getUser()->getValue(),
            $sProduct->getDescription(),

            $uProduct->getId()->getValue(),
            $uProduct->getPrice()->getRegular()->getValue(),
            $uProduct->getPrice()->getOffer()->getValue(),
            $uProduct->getUser()->getValue(),
            $uProduct->getDescription(),

            $u2Product->getDescription(),
        ],[
            $id,
            $mPriceRegular,
            null,
            $mUser,
            $mDescription,

            $id,
            $mPriceRegular,
            $sPriceOffer,
            $sUser,
            $sDescription,

            $id,
            $mPriceRegular,
            $uPriceOffer,
            $uUser,
            $mDescription,

            $u2Description
        ]);
    }
}
