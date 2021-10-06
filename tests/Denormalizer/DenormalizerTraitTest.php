<?php
namespace Terrazza\Component\Serializer\Tests\Denormalizer;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Serializer\Denormalizer\DenormalizerTrait;

class DenormalizerTraitTest extends TestCase {

    function testGetApprovedBuiltInValueNoParameterType() {
        $this->assertEquals(
            $value = 12,
            ((new DenormalizerTraitTestIncludes)->_getApprovedBuiltInValue(null, $value))
        );
    }

    function testGetApprovedBuiltInValueTypeIdentical() {
        $intValue = 12;
        $floatValue = 12.2;
        $this->assertEquals([
            $intValue,
            (string)$intValue,
            (string)$floatValue,
            $floatValue,
        ], [
            ((new DenormalizerTraitTestIncludes)->_getApprovedBuiltInValue("int", $intValue)),
            ((new DenormalizerTraitTestIncludes)->_getApprovedBuiltInValue("string", $intValue)),
            ((new DenormalizerTraitTestIncludes)->_getApprovedBuiltInValue("string", $floatValue)),
            ((new DenormalizerTraitTestIncludes)->_getApprovedBuiltInValue("float", $floatValue)),
        ]
        );
    }
}

class DenormalizerTraitTestIncludes {
    use DenormalizerTrait;
    public function _getApprovedBuiltInValue(?string $parameterType, $inputValue) {
        return $this->getApprovedBuiltInValue($parameterType, $inputValue);
    }
}