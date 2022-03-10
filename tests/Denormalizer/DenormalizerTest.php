<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\Serializer\Tests\_Mocks\Denormalizer;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\_Examples\Model\SerializerExampleTypeString;
use Terrazza\Component\Serializer\Tests\_Examples\Serializer\SerializerRealLifePresentAmount;

class DenormalizerTest extends TestCase {
    /**
     * @throws ReflectionException
     */
    function testInvalidClassName() {
        $denormalizer = Denormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalizeClass("unknownClassName", [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testClassIsInt() {
        $denormalizer = Denormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalizeClass(12, [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testRestrictArguments() {
        $denormalizer = Denormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalizeClass(SerializerRealLifePresentAmount::class, ["value" => 12, "currency" => "Euro"],  true);
    }

    /**
     * @throws ReflectionException
     */
    function testMultipleParamsAsBuiltIn() {
        $denormalizer = Denormalizer::get();
        $object = $denormalizer->denormalizeClass(DenormalizerTestMultipleParams::class,
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
        $denormalizer = Denormalizer::get();
        $objectStringInt                            = $denormalizer->denormalizeClass(SerializerExampleTypeString::class,
            ["string" => $valueInt = 1]);
        $objectStringFloat                          = $denormalizer->denormalizeClass(SerializerExampleTypeString::class,
            ["string" => $valueFloat = 1.1]);
        $objectIntFloat                             = $denormalizer->denormalizeClass(SerializerExampleTypeFloat::class,
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
        $denormalizer = Denormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalizeClass(SerializerExampleTypeFloat::class,
            ["float" => "string"]);
    }

    /**
     * @throws ReflectionException
     */
    function testSetValueDirectly() {
        $denormalizer = Denormalizer::get();
        $object = $denormalizer->denormalizeClass(SerializerExampleTypeFloat::class,
            $float = 12, true, true);
        $this->assertEquals($float, $object->getFloat());
    }

    function testSetValueDirectlyWrongType() {
        $denormalizer = Denormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $object = $denormalizer->denormalizeClass(SerializerExampleTypeFloat::class,
            [$float = 12]);
    }

    // open: [$float = 12] => cause an error!!
    // mapMethodValues, isset($array[0])

    /**
     * @throws ReflectionException
     */
    function testDateTime() {
        $denormalizer = Denormalizer::get();
        $objectDate                                 = $denormalizer->denormalizeClass(SerializerExampleTypeDateTime::class,
            ["date" => $date = "31.01.2021"]);
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }

    /**
     * @throws ReflectionException
     */
    function testUpdateOptionalClassNull() {
        $denormalizer = Denormalizer::get();
        $object                                     = $denormalizer->denormalizeClass(DenormalizerTestUpdateOptionalClass::class,
            ["id" => 12, "optionalClass" => null]);
        $this->assertNull($object->getOptionalClass());
    }

    /**
     * @throws ReflectionException
     */
    function testUpdateRequiredClassNull() {
        $denormalizer = Denormalizer::get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalizeClass(DenormalizerTestUpdateOptionalClass::class,
            ["id" => 12, "requiredClass" => null]);
    }

    /**
     * @throws ReflectionException
     */
    function testOptionalDateTime() {
        $denormalizer = Denormalizer::get();
        $object                                     = $denormalizer->denormalizeClass(DenormalizerTest1OptionalDateTime::class,
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
        $denormalizer = Denormalizer::get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalizeClass(DenormalizerTestProtectedConstructor::class, []);
        $this->assertTrue(true);
    }
}

class DenormalizerTestMultipleParams {
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

class DenormalizerTestProtectedConstructor {
    protected int $value;
    protected function __construct() {}
}

class DenormalizerTest1OptionalDateTime {
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

class DenormalizerTestUpdateOptionalClass {
    private int $id;
    private ?DenormalizerTestMultipleParams $optionalClass;
    private ?DenormalizerTestMultipleParams $requiredClass=null;
    public function __construct(int $id) {
        $this->id               = $id;
        $this->optionalClass    = new DenormalizerTestMultipleParams();
    }
    function getId() : int {
        return $this->id;
    }
    function setRequiredClass(DenormalizerTestMultipleParams $requiredClass) : void {
        $this->requiredClass = $requiredClass;
    }
    function getRequiredClass() :?DenormalizerTestMultipleParams {
        return $this->requiredClass;
    }
    function setOptionalClass(DenormalizerTestMultipleParams $optionalClass=null) : void {
        $this->optionalClass = $optionalClass;
    }
    function getOptionalClass() :?DenormalizerTestMultipleParams {
        return $this->optionalClass;
    }
}