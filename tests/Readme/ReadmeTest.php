<?php
namespace Terrazza\Component\Serializer\Tests\Normalizer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Factory\SerializerFactory;
use Terrazza\Component\Serializer\Factory\DeserializerFactory;
use Terrazza\Component\Serializer\Tests\_Mocks\Logger;
use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\Denormalizer;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\Normalizer;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\Deserializer;

class ReadmeTest extends TestCase {

    function testUnserializerFactory() {
        $logger         = Logger::get();
        $content = json_encode(
            [
                'id' => 12,
                'amount' => 100
            ]
        );

        $deserializer   = new DeserializerFactory($logger);
        $object         = $deserializer->deserialize(ReadmeTargetObject::class, "json", $content);

        $this->assertEquals([
            12,
            100,
        ],[
            $object->getId(),
            $object->getAmount(),
        ]);
    }

    function testSerializerFactory() {
        $logger         = Logger::get();
        $object         = new ReadmeTargetObject(12);
        $object->setAmount(100);
        $serializer     = new SerializerFactory($logger);
        $response       = $serializer->serialize($object, "json");
        $this->assertEquals('{"id":12,"amount":100}', $response);
    }

    function testUnserializeSerialize() {
        $data = [
            'id'        => 1,
            'amount'    => 13
        ];
        $input          = json_encode($data);
        $logger         = Logger::get();
        $deserializer   = (new Deserializer(
            new JsonDecoder(),
            new Denormalizer($logger)
        ));
        $object         = $deserializer->deserialize(ReadmeTargetObject::class, $input);

        $serializer = (new Serializer(
            new JsonEncoder(),
            new Normalizer($logger)
        ));

        $this->assertEquals([
            1,
            13,

            json_encode($data)
        ],[
            $object->getId(),
            $object->getAmount(),

            $serializer->serialize($object)
        ]);
    }

    function testDenormalizerValues() {
        $logger         = Logger::get();
        $object         = new ReadmeTargetObject(12);
        $denormalizer   = new Denormalizer($logger);
        $values         = $denormalizer->denormalizeMethodValues($object, "setAmount", [
            "amount" => 12, "unknown" => 11
        ]);
        $this->assertEquals([
            12
        ], $values);
    }
}

class ReadmeTargetObject {
    public int $id;
    public ?int $amount=null;
    public function __construct(int $id) {
        $this->id = $id;
    }
    public function getId() : int {
        return $this->id;
    }
    public function setAmount(?int $amount) : void {
        $this->amount = $amount;
    }
    public function getAmount() :?int {
        return $this->amount;
    }
}