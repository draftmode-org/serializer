# the serializer component
This component is meant to be used to turn objects into a specific format (XML,JSON,YAML,...) and the other way around.

1. Methods
    1. [Deserializer](#deserializer)
       1. [Decoder](#decode)
       2. [Denormalizer](#denormalize)
          1. [method: denormalize](#denormalize-denormalizeClass)
          2. [method: denormalizeMethodValues](#denormalize-denormalizeMethodValues)
    2. [Serializer](#serializer)
       1. [Normalizer](#normalizer)
       2. [Encoder](#encoder)
    3. [Factory](#factory)
       1. [DeserializerFactory](#deserializer-factory)
       2. [SerializerFactory](#serializer-factory)
2. [Examples](#examples)
    - [Deserialize + Serialize Json (without Factory)](#example-fulfill-json)
    - [Denormalize::denormalizeMethodValues](#example-denormalizeMethodValues)
3. [Install](#install)
4. [Requirements](#require) 

<a id="deserializer" name="deserializer"></a>
<a id="user-content-deserializer" name="user-content-deserializer"></a>
## Deserializer
Deserializer is a combination of 
1. decode (JSON,XML,CSV) a given string into an array
2. denormalize into a class

<a id="decode" name="decode"></a>
<a id="user-content-decode" name="user-content-decode"></a>
### Decode
The decoder converts a given string into an array.
Actually Terrazza/Serializer supports
- JSON
- XML
<a id="denormalize" name="denormalize"></a>
<a id="user-content-denormalize" name="user-content-denormalize"></a>
### Denormalize
The Denormalizer supports two methods.

<a id="denormalize-denormalizeClass" name="denormalize-denormalizeClass"></a>
<a id="user-content-denormalize-denormalizeClass" name="user-content-denormalize-denormalizeClass"></a>
#### method: denormalizeClass
This method convert in input into the given className and
- validate input types
- load/handle nested objects

Properties:
- className (string)
- input (mixed)
- restrictArguments (default: false)

_Business logic_<br>
1. initialize className with __constructor (if public)
2. handle unused arguments with "setter"-methods (if public)

<a id="denormalize-denormalizeMethodValues" name="denormalize-denormalizeMethodValues"></a>
<a id="user-content-denormalize-denormalizeMethodValues" name="user-content-denormalize-denormalizeMethodValues"></a>
#### method: denormalizeMethodValues
This method map/convert given arguments into/based on an object and his methodName.<br><br> 
Properties:
- object
- methodName (string)
- input (mixed)
- restrictArguments (default: false)
 
#### How should a class be designed
We suggest that all required arguments are handled by the __constructor<br>
and all optional arguments are handled by the setter.

<a id="serializer" name="serializer"></a>
<a id="user-content-serializer" name="user-content-serializer"></a>
## Serializer
Serialize is a combination of
1. normalize object to array
2. encode array to (JSON,..)

<a id="normalizer" name="normalizer"></a>
<a id="user-content-normalizer" name="user-content-normalizer"></a>
### Normalizer
The Normalizer converts an object into an array.<br>

_Business logic_<br>
1. try to find for all properties by there "getter" methods (get{}, is{}, has{})
<br><i>found: retrieve related property value</i><br><br>
2. for all properties, public accessible mandatory (not handled by methods)
<br><i>found: retrieve property value</i>

<a id="encode" name="encoder"></a>
<a id="user-content-encoder" name="user-content-encoder"></a>
### Encoder
The encoder converts an array to a string by using.<br><br>
actually provided encodings:
- Json

<a id="factory" name="factory"></a>
<a id="user-content-factory" name="user-content-factory"></a>
## Factory
Every factory covers his parent and provides, "contentType" based, an automatic execution.

<a id="deserializer-factory" name="deserializer-factory"></a>
<a id="user-content-deserializer-factory" name="user-content-deserializer-factory"></a>
### DeserializerFactory
````
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//

use Terrazza\Component\Serializer\Factory\DeserializerFactory;

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

$content = json_encode(
    [
        'id' => 12,
        'amount' => 100
    ]
);

$deserializer   = new DeserializerFactory($logger);
$object         = $deserializer->deserialize(TargetObject::class, "json", $content);
var_dump($object);

/*
class ReadmeTargetObject {
  public int $id =>
  int(12)
  public ?int $amount =>
  int(100)
}
*/
````
<a id="serializer-factory" name="serializer-factory"></a>
<a id="user-content-serializer-factory" name="user-content-serializer-factory"></a>
### SerializerFactory
````
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//

use Terrazza\Component\Serializer\Factory\SerializerFactory;

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

$object         = new ReadmeTargetObject(12);
$object->setAmount(100);
$serializer     = new SerializerFactory($logger);
$response       = $serializer->serialize($object, "json");

var_dump($response);
/*
{"id":12,"amount":100}
*/
````

<a id="examples" name="examples"/></a>
<a id="user-content-examples" name="user-content-examples"/></a>
## Examples

<a id="example-fulfill-json" name="example-fulfill-json"></a>
<a id="user-content-example-fulfill-json" name="user-content-example-fulfill-json"></a>
### Unserialize + Serialize JSON (without Factory)
````
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//

use Terrazza\Component\Serializer\Decoder\JsonDecoder;
use Terrazza\Component\Serializer\Denormalizer;
use Terrazza\Component\Serializer\Encoder\JsonEncoder;
use Terrazza\Component\Serializer\Normalizer;
use Terrazza\Component\Serializer\Serializer;
use Terrazza\Component\Serializer\Deserializer;

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
echo $object->getId();      // 1
echo $object->getName();    // Max 

$serializer = (new Serializer(
    new JsonEncoder(),
    new Normalizer($logger)
));
var_dump(json_encode($data) === $serializer->serialize($object)); // true     
````

<a id="example-denormalizeMethodValues" name="example-denormalizeMethodValues"></a>
<a id="user-content-example-denormalizeMethodValues" name="user-content-example-denormalizeMethodValues"></a>
### Denormalizer::denormalizeMethodValues
````
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//

use \Terrazza\Component\Serializer\Denormalizer;

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
    public function getAmonut() :?int {
        return $this->amount;
    }
}

$object         = new ReadmeTargetObject(12);
$denormalizer   = new Denormalizer($logger);
$values         = $denormalizer->denormalizeMethodValues($object, "setAmount", [
    "amount" => 12, "unknown" => 11
]);
//
// property "unkonwn" has been removed: property does not exists in method
//
var_dump([
            12
        ] === $values);
````

<a id="install" name="install"></a>
<a id="user-content-install" name="user-content-install"></a>
## How to install
### Install via composer
```
composer require terrazza/serializer
```
<a id="require" name="require"></a>
<a id="user-content-require" name="user-content-require"></a>
## Requirements
### php version
- \>= 7.4
### php extension
- ext-json
- ext-libxml
### composer packages
- psr/log
- terrazza/annotation
### composer packages (require-dev)
- terrazza/logger