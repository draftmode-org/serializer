<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RuntimeException;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\Denormalizer\ArrayDenormalizer;
use Terrazza\Component\Serializer\DenormalizerInterface;
use Terrazza\Component\Serializer\Tests\Examples\LoggerUnit;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeDateTime;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeFloat;
use Terrazza\Component\Serializer\Tests\Examples\Model\SerializerExampleTypeString;
use Terrazza\Component\Serializer\Tests\Examples\Serializer\SerializerRealLifePresentAmount;

class ArrayDenormalizerTest extends TestCase {
    function get(bool $log=false) : DenormalizerInterface {
        $logger = LoggerUnit::getLogger("ArrayDenormalizer", $log);
        return new ArrayDenormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
    }

    /**
     * @throws ReflectionException
     */
    function testInvalidClassName() {
        $denormalizer = $this->get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalize("unknownClassName", [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testClassIsInt() {
        $denormalizer = $this->get();
        $this->expectException(RuntimeException::class);
        $denormalizer->denormalize(12, [], true, true);
    }

    /**
     * @throws ReflectionException
     */
    function testRestrictArguments() {
        $denormalizer = $this->get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalize(SerializerRealLifePresentAmount::class, ["value" => 12, "currency" => "Euro"], false, true);
    }

    /**
     * @throws ReflectionException
     */
    function testMultipleParamsAsBuiltIn() {
        $denormalizer = $this->get();
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
        $denormalizer = $this->get();
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
        $denormalizer = $this->get();
        $this->expectException(InvalidArgumentException::class);
        $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            ["float" => "string"]);
    }

    /**
     * @throws ReflectionException
     */
    function testSetValueDirectly() {
        $denormalizer = $this->get();
        $object = $denormalizer->denormalize(SerializerExampleTypeFloat::class,
            $float = 12);
        $this->assertEquals($float, $object->getFloat());
    }

    // open: [$float = 12] => cause an error!!
    // mapMethodValues, isset($array[0])

    /**
     * @throws ReflectionException
     */
    function testDateTime() {
        $denormalizer = $this->get();
        $objectDate                                 = $denormalizer->denormalize(SerializerExampleTypeDateTime::class,
            ["date" => $date = "31.01.2021"]);
        $this->assertEquals($date, $objectDate->date->format("d.m.Y"));
    }

    /**
     * @throws ReflectionException
     */
    public function testProtectedConstructor() {
        $denormalizer = $this->get();
        $denormalizer->denormalize(ArrayDenormalizerTestProtectedConstructor::class, []);
        $this->assertTrue(true);
    }

    /**
     * @throws ReflectionException
     */
    public function testRestrictUninitialized() {
        $denormalizer = $this->get();
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