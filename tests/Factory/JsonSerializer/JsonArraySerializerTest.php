<?php

namespace Terrazza\Component\Serializer\Tests\Factory\JsonSerializer;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifePerson;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifePersonAddress;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeProductUUID;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUserUUID;
use Terrazza\Component\Serializer\Tests\Examples\Deserializer\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentUUID;

class JsonArraySerializerTest extends TestCase {

    function test() {
        $mProduct = new SerializerRealLifeProduct(
            new SerializerRealLifeProductUUID($id = "221"),
        );
        $mProduct->setVLabels(
            new SerializerRealLifeProductLabel($mLabel1 = "mLabel1")
        );
        $mProduct->setALabels([
                new SerializerRealLifeProductLabel($mLabel1)
            ]
        );
        $mProduct->setDescription($mDescription = "mDescription");
        $mProduct->setUser(
            new SerializerRealLifeUserUUID($mUser = "12")
        );
        $mProduct->getPrice()->setRegular(
            new SerializerRealLifeProductAmount($mPriceRegular = 13.12)
        );
        $mProduct->setPerson(
            new SerializerRealLifePerson($mPersonName = "pPersonName")
        );
        $mProduct->getPerson()->setAddress(
            new SerializerRealLifePersonAddress($mAddressStreet="mAddressStreet", $mAddressCity="mAddressCity")
        );

        $serializer = JsonArrayUnit::getSerializer(false, [
            SerializerRealLifeUUID::class => SerializerRealLifePresentUUID::class,
            SerializerRealLifeProductAmount::class => SerializerRealLifePresentAmount::class,
        ]);
        $response = $serializer->serialize($mProduct);
        $this->assertTrue(true);
    }
}