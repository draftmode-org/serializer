<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\_Mocks\ArrayDenormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifePerson;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifePersonAddress;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductLabel;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProductUUID;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeUserUUID;

class JsonDeserializerRealLifeExampleTest extends TestCase {

    /**
     * @throws \ReflectionException
     */
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
        ], "mProduct");

        /** @var SerializerRealLifeProduct $sProduct */
        /** @var SerializerRealLifeProduct $uProduct */
        /** @var SerializerRealLifeProduct $u2Product */
        /** @var SerializerRealLifeProduct $u3Product */
        //
        //
        // create with serializer
        //
        $denormalize= ArrayDenormalizer::get();
        $sProduct   = $denormalize->denormalize(SerializerRealLifeProduct::class, [
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
        ]);

        $this->assertEquals([
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
        ],[
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
        ], "sProduct");

        //
        // update with serializer
        //
        $denormalize= ArrayDenormalizer::get();
        $uProduct   = $denormalize->denormalize($mProduct, [
            'vLabels'       => null,
            'price'         => [
                'offer'         => $uPriceOffer = 12.1
            ],
            'user'          => $uUser = "uUser",
            'aLabels'       => null,
            'person'        => [
                'address'       => [
                    'street'        => $uAddressStreet = "uAddressStreet"
                ]
            ]
        ]);

        $this->assertEquals([
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
        ],[
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
        ],"uProduct");

        //
        // update with serializer
        //
        $denormalize= ArrayDenormalizer::get();
        $u2Product  = $denormalize->denormalize($mProduct, [
            'description'   => $u2Description = null,
            'person'        => null
        ], false, true);

        $this->assertEquals([
            $u2Product->getDescription(),
            $u2Product->getPerson(),
        ],[
            $u2Description,
            null,
        ], "u2Product");

        //
        // update with serializer
        //
        $denormalize= ArrayDenormalizer::get();
        $u3Product  = $denormalize->denormalize($u2Product, [
            'person'        => [
                'name'      => $u3PersonName = "u3PersonName",
                'address'       => [
                    'street'        => $uAddressStreet,
                    'city'          => $u3AddressCity = "u3AddressCity"
                ]
            ]
        ]);

        $this->assertEquals([
            $u3Product->getPerson()->getName(),
            $u3Product->getPerson()->getAddress()->getStreet(),
            $u3Product->getPerson()->getAddress()->getCity(),
        ],[
            $u3PersonName,
            $uAddressStreet,
            $u3AddressCity,
        ], "u3Product");
    }
}
