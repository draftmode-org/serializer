<?php

namespace Terrazza\Component\Serializer\Annotation;

class AnnotationParameter implements AnnotationTypeInterface {
    use AnnotationTypeTrait;
    private bool $variadic=false;
    private bool $defaultValueAvailable=false;
    /** @var mixed  */
    private $defaultValue=null;

    public function isVariadic() : bool {
        return $this->variadic;
    }
    public function setVariadic(bool $variadic) : void {
        $this->variadic = $variadic;
    }

    public function setDefaultValueAvailable(bool $available):void {
        $this->defaultValueAvailable=$available;
    }
    public function isDefaultValueAvailable() : bool {
        return $this->defaultValueAvailable;
    }

    /**
     * @param mixed $value
     */
    public function setDefaultValue($value): void {
        $this->defaultValue = $value;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }
}