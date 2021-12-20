<?php
namespace Terrazza\Component\Serializer\Tests\Normalizer;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\ReflectionClass\ClassNameResolver;
use Terrazza\Component\Serializer\Annotation\AnnotationFactory;
use Terrazza\Component\Serializer\INameConverter;
use Terrazza\Component\Serializer\Normalizer\ArrayNormalizer;
use Terrazza\Component\Serializer\INormalizer;
use Terrazza\Component\Serializer\Tests\_Mocks\LoggerMock;

class ArrayNormalizerTest extends TestCase {
    function get(bool $log=false) : INormalizer {
        $logger = LoggerMock::get($log);
        return new ArrayNormalizer(
            $logger,
            new AnnotationFactory(
                $logger,
                new ClassNameResolver()
            )
        );
    }

    function testSuccess() {
        $normalizer     = $this->get();
        $response       = $normalizer->normalize(new ArrayNormalizerTestSuccessful($number=12, $optional=true, $default=12));
        $this->assertEquals(["number" => $number, 'optional' => $optional, 'default' => $default, "pNull" => null], $response);
    }

    function testPropertyNotInitialized() {
        $normalizer     = $this->get();
        $this->expectException(RuntimeException::class);
        $normalizer->normalize(new ArrayNormalizerTestPropertyNotInitialized);
    }

    function testUndefinedType() {
        $normalizer     = $this->get();
        $this->expectException(RuntimeException::class);
        $normalizer->normalize(new ArrayNormalizerTestUndefinedType(12));
    }

    function testNameConverterDoesNotExists() {
        $normalizer     = $this->get();
        $normalizer     = $normalizer->withNameConverter([
            ArrayNormalizerTestEmbeddedClass::class => "unknownConverterClass"
        ]);
        $this->expectException(RuntimeException::class);
        $normalizer->normalize(new ArrayNormalizerTestWithClass(new ArrayNormalizerTestEmbeddedClass(12)));
    }

    function testNameConverterInterfaceFailure() {
        $normalizer     = $this->get();
        $normalizer     = $normalizer->withNameConverter([
            ArrayNormalizerTestEmbeddedClass::class => ArrayNormalizerTestEmbeddedClass::class
        ]);
        $this->expectException(RuntimeException::class);
        $normalizer->normalize(new ArrayNormalizerTestWithClass(new ArrayNormalizerTestEmbeddedClass(12)));
    }

    function testNameConverterGetFailure() {
        $normalizer     = $this->get();
        $normalizer     = $normalizer->withNameConverter([
            ArrayNormalizerTestEmbeddedClass::class => ArrayNormalizerTestEmbeddedClassPresenter::class
        ]);
        $this->expectException(RuntimeException::class);
        $normalizer->normalize(new ArrayNormalizerTestWithClass(new ArrayNormalizerTestEmbeddedClass(12)));
    }
}

class ArrayNormalizerTestSuccessful {
    public int $number;
    public bool $optional;
    public bool $default;
    private int $private=12;
    private ?int $pNull=null;
    public static int $static=12;
    public function __construct(int $number, bool $optional, bool $default) {
        $this->number = $number;
        $this->optional = $optional;
        $this->default = $default;
    }
    public function isOptional(): bool {
        return $this->optional;
    }
    public function hasDefault() : bool {
        return $this->default;
    }
    public function getPNull():?int {
        return $this->pNull;
    }
}

class ArrayNormalizerTestPropertyNotInitialized {
    public int $number;
}

class ArrayNormalizerTestUndefinedType {
    public $number;
    public function __construct($number) {
        $this->number = $number;
    }
}
class ArrayNormalizerTestEmbeddedClassPresenter implements INameConverter {
    private ArrayNormalizerTestEmbeddedClass $number;
    public function __construct(ArrayNormalizerTestEmbeddedClass $number) {
        $this->number = $number;
    }
    public function getValue() : int {
        return ($this->number->getValue() / 0);
    }
}
class ArrayNormalizerTestEmbeddedClass {
    private int $number;
    public function __construct(int $number) {
        $this->number = $number;
    }
    public function getValue() : int {
        return $this->number;
    }
}
class ArrayNormalizerTestWithClass {
    public ArrayNormalizerTestEmbeddedClass $number;
    public function __construct(ArrayNormalizerTestEmbeddedClass $number) {
        $this->number = $number;
    }
}