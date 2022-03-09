<?php
namespace Terrazza\Component\Serializer\Tests\Factory\Json;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\DeserializerFactory;
use Terrazza\Component\Serializer\Tests\_Mocks\ConverterFactory;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerRealLifeProduct;

class JsonSerializeDeserializeTest extends TestCase {

    function testBoth() {
        $input = json_encode($arrInput = [
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
        $deserializer   = ConverterFactory::getDeserializer();
        $sProduct       = $deserializer->deserialize(SerializerRealLifeProduct::class, $input, "json", true);

        $serializer     = ConverterFactory::getSerializer();
        $output         = $serializer->serialize($sProduct, "json");

        $arrInput["person"]["address"]["zip"] = null;
        $input          = json_encode($arrInput);
        $this->assertEquals($input, $output);
    }
}