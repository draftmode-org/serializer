# the serializer component
This component is meant to be used to turn objects into a specific format (XML,JSON,YAML,...) and the other way around.

1. Methods
    1. [Deserialize](#deserialize)
2. [Install](#install)
3. [Requirements](#require)
4. [Examples](#examples)
    - [Json](#examples-json)

<a id="deserialize" name="deserialize"></a>
<a id="user-content-deserialize" name="user-content-deserialize"></a>
## Method - Deserialize
1. decode (JSON,XML,CSV) into an array
2. denormalize into an object

Terrazza/Serializer supports two methods.<br>
a) create a new object<br>
b) update an existing object

The difference: call deserialize with a className or an existing object.

In case of create an object:
1. try to handle __constructor (if he is public)
2. handled unused arguments with "setter"-methods (if they are public)

In case of update an object:<br>
<i>notice:<br>
the constructor will not be used!<br>
only if the current value of an valueObject is null and you want to update the valueObject.
</i>
1. clone input object (unserialize(serialize($object)))
2. handle all arguments with "setter"-methods (if they are public)

in any case there are 2 options provided
- restrictUnInitialized (default: false)
- restrictArguments (default: false)

### How should the class be designed
We suggest that all required arguments are handled by the __constructor<br>
and all optional arguments are handled by the setter.<br>

#### Actually provided decoding
- JSON
<a id="deserialize" name="deserialize"></a>
<a id="user-content-deserialize" name="user-content-deserialize"></a>
## Method - Serialize
This method is currently not supported, will be, but isn´t 
<a id="install" name="install"></a>
<a id="user-content-install" name="user-content-install"></a>
## How to install
### Install via composer
```
composer require terrazza/component-serializer
```
<a id="require" name="require"></a>
<a id="user-content-require" name="user-content-require"></a>
## Requirements
### php version
- \>= 7.4
### php extension 
- ext-json
### composer packages
- terrazza/reflectionclass

<a id="examples" name="examples"/></a>
<a id="user-content-examples" name="user-content-examples"/></a>
## Examples

<a id="examples-json" name="examples-json"></a>
<a id="user-content-examples-json" name="user-content-examples-json"></a>
### deserialize JSON (create)
```php
$input = json_encode(
    [
        'id' => 1,
        'name' => 'Max'
    ]
);
$object = (new JsonArraySerializer)
    ->deserialize(TargetObject::class, $input);
   
echo $object->getId(); // 1
echo $object->getName(); // Max 

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
### deserialize JSON (update)
```php
$input = json_encode(
    [
        'id' => 1,
        'name' => 'Max'
    ]
);
// create object
$object = (new JsonArraySerializer)
    ->deserialize(TargetObject::class, $input);

// update object
$input = json_encode(
    [
        'id' => 2,
        'name' => 'Update'
    ]
);    
$update = (new JsonArraySerializer)
    ->deserialize($object, $input);    
      
echo $object->getId(); // 1, cause constructor will be ignored
echo $object->getName(); // Update

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