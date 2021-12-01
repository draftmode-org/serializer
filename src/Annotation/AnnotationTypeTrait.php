<?php

namespace Terrazza\Component\Serializer\Annotation;

trait AnnotationTypeTrait {
    private string $name;
    private bool $array=false;
    private bool $builtIn=false;
    private bool $optional=false;
    private ?string $type=null;
    private ?string $declaringClass=null;

    public function __construct (string $name) {
        $this->name = $name;
    }

    public function isArray() : bool {
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

    public function isOptional() : bool {
        return $this->optional;
    }
    public function setOptional(bool $optional) : void {
        $this->optional = $optional;
    }

    public function setType(string $type) : void {
        $this->type = $type;
    }
    public function getType() :?string {
        return $this->type;
    }

    public function setDeclaringClass(?string $declaringClass) : void {
        $this->declaringClass                       = $declaringClass;
    }
    public function hasDeclaringClass() : bool {
        return (bool)$this->declaringClass;
    }
    public function getDeclaringClass() : ?string {
        return $this->declaringClass;
    }
}