<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\Serializer\Tests\_Mocks\ArrayDenormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeString;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentAmount;

class ArrayDenormalizerTest extends TestCase {
    /**
     * @throws ReflectionException
     */
    function testInvalidClassName() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalize("unknownClassName", [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testClassIsInt() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalize(12, [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testRestrictArguments() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalize(SerializerRealLifePresentAmount::class, ["value" => 12, "currency" => "Euro"], false, true);
    }

    /**
     * @throws ReflectionException
     */
    function testMultipleParamsAsBuiltIn() {
        $denormalizer = ArrayDenormalizer::get();
        $object = $denormalizer->denormalize(ArrayDenormalizerTestMultipleParams::class,
            ["name" => ["firstName" => $firstName = "f", "lastName" => $lastName = "l"]]
        );
        $this->assertEquals([
            $firstName,
            $lastName
        ],[
            $object->getFirstName(),
            $object->getLastName()
        ]);
    }

    /**
     * @throws ReflectionException
     */
    function testTypeSwitch() {
        $denormalizer = ArrayDenormalizer::get();
        $objectStringInt                            = $denormalizer->denormalize(SerializerExampleTypeString::class,
            ["string" => $valueInt = 1]);
        $objectStringFloat                          = $denormalizer->denormalize(SerializerExampleTypeString::class,
            ["string" => $valueFloat = 1.1]);
        $objectIntFloat                             = $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            ["float" => $valueInt]);
        $this->assertEquals([
            (string)$valueInt,
            (string)$valueFloat,
            $valueInt
        ], [
            $objectStringInt->string,
            $objectStringFloat->string,
            $objectIntFloat->float,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    function testTypeFailure() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            ["float" => "string"]);
    }

    /**
     * @throws ReflectionException
     */
    function testSetValueDirectly() {
        $denormalizer = ArrayDenormalizer::get();
        $object = $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            $float = 12, true, true);
        $this->assertEquals($float, $object->getFloat());
    }

    function testSetValueDirectlyWrongType() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $object = $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            [$float = 12]);
    }

    // open: [$float = 12] => cause an error!!
    // mapMethodValues, isset($array[0])

    /**
     * @throws ReflectionException
     */
    function testDateTime() {
        $denormalizer = ArrayDenormalizer::get();
        $objectDate                                 = $denormalizer->denormalize(SerializerExampleTypeDateTime::class,
            ["date" => $date = "31.01.2021"]);
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }

    function testOptionalDateTime() {
        $denormalizer = ArrayDenormalizer::get();
        $object                                     = $denormalizer->denormalize(ArrayDenormalizerTest1OptionalDateTime::class,
            ["id" => $id = 12]);
        $this->assertEquals([
            $id,
            null
        ], [
            $object->getId(),
            $object->getUpdated()
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function testProtectedConstructor() {
        $denormalizer = ArrayDenormalizer::get();
        $denormalizer->denormalize(ArrayDenormalizerTestProtectedConstructor::class, []);
        $this->assertTrue(true);
    }

    /**
     * @throws ReflectionException
     */
    public function testRestrictUninitialized() {
        $denormalizer = ArrayDenormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalize(ArrayDenormalizerTestProtectedConstructor::class, [], true);
    }
}

class ArrayDenormalizerTestMultipleParams {
    private ?string $firstName=null;
    private ?string $lastName=null;
    function setName(string $firstName, string $lastName) : void {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
    function getFirstName() :?string {
        return $this->firstName;
    }
    function getLastName() :?string {
        return $this->lastName;
    }
}

class ArrayDenormalizerTestProtectedConstructor {
    protected int $value;
    protected function __construct() {}
}

class ArrayDenormalizerTest1OptionalDateTime {
    private int $id;
    private ?DateTime $updated;
    public function __construct(int $id, ?DateTime $updated) {
        $this->id = $id;
        $this->updated = $updated;
    }
    function getId() : int {
        return $this->id;
    }
    function getUpdated() :?DateTime {
        return $this->updated;
    }
}