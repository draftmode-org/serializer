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
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;

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

        $serializer = JsonArrayUnit::getSerializer();
        $response = $serializer->serialize($mProduct);
        $this->assertEquals(<<<JSON
        {"id":"{$id}","price":{"regular":{$mPriceRegular},"offer":null},"user":"{$mUser}","description":"{$mDescription}","vLabels":["{$mLabel1}"],"aLabels":["{$mLabel1}"],"person":{"name":"{$mPersonName}","address":{"street":"{$mAddressStreet}","city":"{$mAddressCity}"}}}
        JSON, $response);
    }

    function testWithNull() {
        $mProduct = new SerializerRealLifeProduct(
            new SerializerRealLifeProductUUID($id = "221"),
        );
        $serializer = JsonArrayUnit::getSerializer();
        $response = $serializer->serialize($mProduct);
        $this->assertEquals(<<<JSON
        {"id":"221","price":{"regular":null,"offer":null},"user":null,"description":null,"vLabels":[],"aLabels":[],"person":null}
        JSON, $response);
    }
}