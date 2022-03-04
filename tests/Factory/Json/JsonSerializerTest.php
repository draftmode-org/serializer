<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\_Mocks\JsonFactory;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifePerson;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifePersonAddress;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductUUID;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeUserUUID;

class JsonSerializerTest extends TestCase {

    function testFull() {
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
        $mProduct->setCreatedAt(new \DateTime($dateTime = "2021-01-31"));

        $serializer = JsonFactory::getSerializer();
        $response = $serializer->serialize($mProduct, JsonFactory::getNameConverter());
        $this->assertEquals(<<<JSON
        {"id":"{$id}","price":{"regular":{$mPriceRegular},"offer":null},"user":"{$mUser}","description":"{$mDescription}","vLabels":["{$mLabel1}"],"aLabels":["{$mLabel1}"],"person":{"name":"{$mPersonName}","address":{"street":"{$mAddressStreet}","city":"{$mAddressCity}"}},"createdAt":"{$dateTime}"}
        JSON, $response);
    }

    function testWithNull() {
        $mProduct = new SerializerRealLifeProduct(
            new SerializerRealLifeProductUUID($id = "221"),
        );
        $serializer = JsonFactory::getSerializer();
        $response   = $serializer->serialize($mProduct);
        $this->assertEquals(<<<JSON
        {"id":"221","price":{"regular":null,"offer":null},"user":null,"description":null,"vLabels":[],"aLabels":[],"person":null,"createdAt":null}
        JSON, $response);
    }
}