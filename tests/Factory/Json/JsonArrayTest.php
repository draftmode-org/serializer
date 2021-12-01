<?php
namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProduct;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeProductAmount;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerRealLifeUUID;
use Terrazza\Component\Serializer\Tests\Examples\JsonArrayUnit;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentAmount;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentUUID;

class JsonArrayTest extends TestCase {

    function testBoth() {
        $input = json_encode([
            'id'            => "121",
            'price' => [
                'regular'   => 95.40,
                'offer'     => 90.0
            ],
            'user'          => "sUser",
            'description'   => "sDescription",
            'vLabels'        => [
                "sLabel1",
                "sLabel2",
            ],
            'aLabels'        => [
                "aLabel1",
                "aLabel1",
            ],
            'person'        => [
                'name'          => "mPersonName",
                'address'       => [
                    'street'        => "mAddressStreet",
                    'city'          => "mAddressCity",
                ]
            ],
            'createdAt'     => "2021-01-31"
        ]);
        $deserializer = JsonArrayUnit::getDeserializer();
        $sProduct   = $deserializer->deserialize(SerializerRealLifeProduct::class, $input, true, true);

        $serializer = JsonArrayUnit::getSerializer();
        $output     = $serializer->serialize($sProduct);

        $this->assertEquals($input, $output);
    }
}