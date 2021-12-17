<?php

namespace Terrazza\Component\Serializer\Annotation;

interface IAnnotationType {
    public function isArray() : bool;
    public function setArray(bool $array) : void;
    public function getName() : string;
    public function __toString() : string;
    public function isBuiltIn() : bool;
    public function setBuiltIn(bool $builtIn) : void;
    public function isOptional() : bool;
    public function setOptional(bool $optional) : void;
    public function setType(string $type) : void;
    public function getType() :?string;
    public function setDeclaringClass(?string $declaringClass) : void;
    public function hasDeclaringClass() : bool;
    public function getDeclaringClass() : ?string;
}