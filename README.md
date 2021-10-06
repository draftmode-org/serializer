# the serializer component
This component is for serialize and deserialize.

Actually provided types
- JSON
    - method: deserialize
<hr>
    
1. [How to install](#install)
2. [Requirements](#require)
3. [Examples](#examples)
   - [Json](#examples-json)

<a id="install" name="install"></a>
<a id="user-content-install" name="user-content-install"></a>
## How to install
### Install via composer
```
composer require terrazza/component-serializer
```
<a name="require"></a>
<a name="user-content-require"></a>
## Requirements
### php version
- \>= 7.4
### php extension 
- ext-json
### composer packages
- terrazza/component-serializer

<a name="examples"/></a>
<a name="user-content-examples"/></a>
## Examples

<a name="examples-json"></a>
<a name="user-content-examples-json"></a>
### deserialize JSON
```php
$input = json_encode(
    [
        'id' => 1,
    ]
);
$object = (new JsonSerializerFactory)
    ->deserialize(TargetObject::class, $input);
   
echo $object->getId(); // 1 

class TargetObject {
    public int $id;
    public function __construct(int $id) {
        $this->id = $id;
    }
    public function getId() : int {
        return $this->id;
    }
}
```