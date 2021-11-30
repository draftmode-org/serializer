<?php

namespace Terrazza\Component\Serializer\Annotation;

use ReflectionClass;

class AnnotationParameter {
    private string $name;
    private bool $array=false;
    private bool $builtIn=false;
    private bool $variadic=false;
    private bool $optional=false;
    private bool $defaultValueAvailable=false;
    private $defaultValue=null;
    private ?string $type=null;
    private ?string $declaringClass=null;
    public function __construct (string $name) {
        $this->name = $name;
    }
    function isArray() : bool {
        return $this->array;
    }
    public function setArray(bool $array) : void {
        $this->array = $array;
    }

    public function getName() : string {
        return $this->name;
    }
    public function __toString() : string {
        return $this->name;
    }

    public function isBuiltIn() : bool {
        return $this->builtIn;
    }
    public function setBuiltIn(bool $builtIn) : void {
        $this->builtIn = $builtIn;
    }

    public function isVariadic() : bool {
        return $this->variadic;
    }
    public function setVariadic(bool $variadic) : void {
        $this->variadic = $variadic;
    }

    public function setDeclaringClass(?string $declaringClass) : void {
        $this->declaringClass                       = $declaringClass;
    }
    public function getDeclaringClass() : ?string {
        return $this->declaringClass;
    }

    public function isOptional() : bool {
        return $this->optional;
    }
    public function setOptional(bool $optional) : void {
        $this->optional = $optional;
    }

    public function setDefaultValueAvailable(bool $available):void {
        $this->defaultValueAvailable=$available;
    }
    public function isDefaultValueAvailable() : bool {
        return $this->defaultValueAvailable;
    }

    public function setDefaultValue($value): void {
        $this->defaultValue = $value;
    }
    public function getDefaultValue() {
        return $this->defaultValue;
    }

    public function setType(string $type) : void {
        $this->type = $type;
    }
    public function getType() :?string {
        return $this->type;
    }
}