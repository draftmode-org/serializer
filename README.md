# the serializer component
This component is meant to be used to turn objects into a specific format (XML,JSON,YAML,...) and the other way around.

1. Methods
    1. [Deserialize](#method---deserialize)
       1. [Decode](#decode)
       2. [Denormalize](#denormalize)
    2. [Serialize](#method---serialize)
       1. [Normalize](#normalize)
       2. [Encode](#encode)
2. [Install](#install)
3. [Requirements](#require)
4. [Examples](#examples)
    - [Json](#examples-json)

<a id="deserialize" name="deserialize"></a>
<a id="user-content-deserialize" name="user-content-deserialize"></a>
## Method - Deserialize
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
The Denormalizer supports two methods.<br>
a) create a new object<br>
<i>input: class-method</i><br>
b) update an existing object<br>
<i>input: existing class</i>

in any case 2 options are provided
- restrictUnInitialized (default: false)
- restrictArguments (default: false)

#### Logic: create an object
1. try to handle __constructor (if he is public)
2. handled unused arguments with "setter"-methods (if they are public)

#### Logic: update an object<br>
<i>notice:<br>
The common way is, to ignore the __constructor and just update based on setter methods.
</i>
1. clone input object ```unserialize(serialize($object))```
2. handle all arguments with "setter"-methods (if they are public)

#### How should a class be designed
We suggest that all required arguments are handled by the __constructor<br>
and all optional arguments are handled by the setter.

<a id="serialize" name="serialize"></a>
<a id="user-content-serialize" name="user-content-serialize"></a>
## Method - Serialize
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
- terrazza/reflectionclass
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
// $logger has to be a psr/log/LoggerInterface implementation
//
$object = (new JsonDeserializer($logger))
    ->deserialize(TargetObject::class, $input);
   
echo $object->getId(); // 1
echo $object->getName(); // Max 

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
### Deserialize + Serialize JSON (update)
```php
$input = json_encode(
    [
        'id' => 1,
        'name' => 'Max'
    ]
);
//
// $logger has to be a psr/log/LoggerInterface implementation
//

// create object
$object = (new JsonDeserializer($logger))
    ->deserialize(TargetObject::class, $input);

// update object
$input = json_encode(
    [
        'id' => 2,
        'name' => 'Update'
    ]
);    
$object = (new JsonDeserializer($logger))
    ->deserialize($object, $input);    
      
echo $object->getId(); // 1, cause constructor will be ignored
echo $object->getName(); // Update

$json = (new JsonSerializer($logger))
    ->serialize($object);
    
var_dump(json_encode([
    'id' => 1,
    'name' => "Update"
]) === $json);

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