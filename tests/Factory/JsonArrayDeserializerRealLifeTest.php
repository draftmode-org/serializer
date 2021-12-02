<?php

namespace Terrazza\Component\Serializer\Tests\Factory\Json;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifePerson;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifePersonAddress;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductUUID;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeUserUUID;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;

class JsonArrayDeserializerRealLifeTest extends TestCase {

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
        /** @var SerializerRealLifeProduct $sProduct */
        /** @var SerializerRealLifeProduct $uProduct */
        /** @var SerializerRealLifeProduct $u2Product */
        /** @var SerializerRealLifeProduct $u3Product */
        //
        $serializer = JsonArrayUnit::getDeserializer();
        //
        // create with serializer
        //
        $sProduct   = $serializer->deserialize(SerializerRealLifeProduct::class, json_encode([
            'id'            => $id,
            'price' => [
                'regular'   => $mPriceRegular,
                'offer'     => $sPriceOffer = 12.0
            ],
            'vLabels'        => [
                $sLabel1    = "sLabel1",
                $sLabel2    = "sLabel2",
            ],
            'aLabels'        => [
                $sLabel1,
                $sLabel2,
            ],
            'description'   => $sDescription = "sDescription",
            'person'        => [
                'name'          => $mPersonName,
                'address'       => [
                    'street'        => $mAddressStreet,
                    'city'          => $mAddressCity,
                ]
            ],
            'user'          => $sUser = "sUser",
        ]));

        //
        // update with serializer
        //
        $serializer = JsonArrayUnit::getDeserializer();
        $uProduct   = $serializer->deserialize($mProduct, json_encode([
            'user'          => $uUser = "uUser",
            'vLabels'       => null,
            'aLabels'       => null,
            'price'         => [
                'offer'         => $uPriceOffer = 12.1
            ],
            'person'        => [
                'address'       => [
                    'street'        => $uAddressStreet = "uAddressStreet"
                ]
            ]
        ]));

        $serializer = JsonArrayUnit::getDeserializer();
        $u2Product  = $serializer->deserialize($mProduct, json_encode([
            'description'   => $u2Description = null,
            'person'        => null
        ]), false, true);

        $serializer = JsonArrayUnit::getDeserializer();
        $u3Product  = $serializer->deserialize($u2Product, json_encode([
            'person'        => [
                'name'      => $u3PersonName = "u3PersonName",
                'address'       => [
                    'street'        => $uAddressStreet,
                    'city'          => $u3AddressCity = "u3AddressCity"
                ]
            ]
        ]));

        $this->assertEquals([
            $mProduct->getId()->getValue(),
            $mProduct->getPrice()->getRegular()->getValue(),
            $mProduct->getPrice()->getOffer()->getValue(),
            $mProduct->getUser()->getValue(),
            $mProduct->getDescription(),
            $mProduct->getVLabels(),
            $mProduct->getALabels(),
            $mProduct->getPerson()->getName(),
            $mProduct->getPerson()->getAddress()->getStreet(),
            $mProduct->getPerson()->getAddress()->getCity(),

            $sProduct->getId()->getValue(),
            $sProduct->getPrice()->getRegular()->getValue(),
            $sProduct->getPrice()->getOffer()->getValue(),
            $sProduct->getUser()->getValue(),
            $sProduct->getDescription(),
            $sProduct->getVLabels(),
            $sProduct->getALabels(),
            $sProduct->getPerson()->getName(),
            $sProduct->getPerson()->getAddress()->getStreet(),
            $sProduct->getPerson()->getAddress()->getCity(),

            $uProduct->getId()->getValue(),
            $uProduct->getPrice()->getRegular()->getValue(),
            $uProduct->getPrice()->getOffer()->getValue(),
            $uProduct->getUser()->getValue(),
            $uProduct->getDescription(),
            $uProduct->getVLabels(),
            $uProduct->getALabels(),
            $uProduct->getPerson()->getName(),
            $uProduct->getPerson()->getAddress()->getStreet(),
            $uProduct->getPerson()->getAddress()->getCity(),

            $u2Product->getDescription(),
            $u2Product->getPerson(),

            $u3Product->getPerson()->getName(),
            $u3Product->getPerson()->getAddress()->getStreet(),
            $u3Product->getPerson()->getAddress()->getCity(),
        ],[
            $id,
            $mPriceRegular,
            null,
            $mUser,
            $mDescription,
            [new SerializerRealLifeProductLabel($mLabel1)],
            [new SerializerRealLifeProductLabel($mLabel1)],
            $mPersonName,
            $mAddressStreet,
            $mAddressCity,

            $id,
            $mPriceRegular,
            $sPriceOffer,
            $sUser,
            $sDescription,
            [new SerializerRealLifeProductLabel($sLabel1), new SerializerRealLifeProductLabel($sLabel2)],
            [new SerializerRealLifeProductLabel($sLabel1), new SerializerRealLifeProductLabel($sLabel2)],
            $mPersonName,
            $mAddressStreet,
            $mAddressCity,

            $id,
            $mPriceRegular,
            $uPriceOffer,
            $uUser,
            $mDescription,
            [],
            [],
            $mPersonName,
            $uAddressStreet,
            $mAddressCity,

            $u2Description,
            null,

            $u3PersonName,
            $uAddressStreet,
            $u3AddressCity,
        ]);
    }
}
