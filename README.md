# the serializer component
This component is meant to be used to turn objects into a specific format (XML,JSON,YAML,...) and the other way around.

1. Methods
    1. [Deserialize](#method---deserialize)
       1. [Decode](#decode)
       2. [Denormalize](#denormalize)
          1. [method: denormalize](#denormalize-denormalize)
          2. [method: denormalizeMethodValues](#denormalize-denormalizeMethodValues)
    2. [Serialize](#method---serialize)
       1. [Normalize](#normalize)
       2. [Encode](#encode)
2. [Install](#install)
3. [Requirements](#require)
4. [Examples](#examples)
    - [Json](#examples-json)

<a id="deserialize" name="deserialize"></a>
<a id="user-content-deserialize" name="user-content-deserialize"></a>
## Deserialize
Deserialize is a combination of 
1. decode (JSON,XML,CSV) into an array
2. denormalize into an object

<a id="deserialize" name="decode"></a>
<a id="user-content-decode" name="user-content-decode"></a>
### Decode
The decoder converts an input object to an array.
Actually Terrazza/Serializer supports
- JSON
- XML
<a id="denormalize" name="denormalize"></a>
<a id="user-content-denormalize" name="user-content-denormalize"></a>
### Denormalize
The Denormalizer supports two methods.

<a id="denormalize-denormalize" name="denormalize-denormalize"></a>
<a id="user-content-denormalize-denormalize" name="user-content-denormalize-denormalize"></a>
#### method: denormalize
This method convert in input into the given className and
- validate input types
- load/handle nested objects

Properties:
- className (string)
- input (mixed)
- restrictUnInitialized (default: false)
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

<a id="serialize" name="serialize"></a>
<a id="user-content-serialize" name="user-content-serialize"></a>
## Serialize
Serialize is a combination of
1. normalize object to array
2. encode array to (JSON,XML,CSV,..)

<a id="normalize" name="normalize"></a>
<a id="user-content-normalize" name="user-content-normalize"></a>
### Normalize
the order to get the properties of an object is:
1. try to find for all properties there "getter" (get{}, is{}, has{})
<br><i>found: retrieve his related property</i><br><br>
2. for all properties, public accessible mandatory
<br><i>found: retrieve property</i>

<a id="encode" name="encode"></a>
<a id="user-content-encode" name="user-content-encode"></a>
### Encode
Teh Encoder converts an array to an output format (e.g. JSON)

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

<a id="examples" name="examples"/></a>
<a id="user-content-examples" name="user-content-examples"/></a>
## Examples

<a id="examples-json" name="examples-json"></a>
<a id="user-content-examples-json" name="user-content-examples-json"></a>
### Deserialize + Serialize JSON (create)
```php
$input = json_encode(
    [
        'id' => 1,
        'name' => 'Max'
    ]
);
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//
use Terrazza\Component\Serializer\Factory\Json\JsonSerializer;
use Terrazza\Component\Serializer\Factory\Json\JsonDeserializer;

$object = (new JsonDeserializer($logger))
    ->deserialize(TargetObject::class, $input);
   
echo $object->getId();      // 1
echo $object->getName();    // Max 

$json = (new JsonSerializer($logger))
    ->serialize($object);
    
var_dump($input === $json); // true    

class TargetObject {
    public int $id;
    public ?string $name=null;
    public function __construct(int $id) {
        $this->id = $id;
    }
    public function getId() : int {
        return $this->id;
    }
    public function getName() :?string {
        return $this->name;
    }    
    public function setName(?string $name) : void {
        $this->name = $name;
    }    
}
```
### Deserialize::denormalizeMethodValues
```php
use Terrazza\Component\Serializer\Factory\Json\JsonDeserializer;
use \Terrazza\Component\Serializer\Denormalizer;

$input = json_encode(
    [
        'id' => 1,
        'name' => 'Max'
    ]
);
//
// $logger has to be a Psr\Log\LoggerInterface implementation
//

// create object
$object = (new JsonDeserializer($logger))
    ->deserialize(TargetObject::class, $input);

$values = (new Denormalizer($logger))->denormalizeMethodValues($object, ["amount" => 12]);

var_dump([
    'amount' => 12
] === $values);

class TargetObject {
    public int $id;
    public ?string $name=null;
    public ?int $amount=null;
    public function __construct(int $id) {
        $this->id = $id;
    }
    public function getId() : int {
        return $this->id;
    }
    public function getName() :?string {
        return $this->name;
    }    
    public function setName(?string $name) : void {
        $this->name = $name;
    }    
    public function setAmount(?int $amount) : void {
        $this->amount = $amount;
    }
    public function getAmonut() :?int {
        return $this->amount;
    }
}
```