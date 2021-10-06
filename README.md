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

<a name="install"></a>
## How to install
### Install via composer
```
composer require terrazza/component-serializer
```
<a name="require"></a>
## Requirements
### php version
- \>= 7.4
### php extension 
- ext-json

<a name="examples"/></a>
## Examples

<a name="examples-json"></a>
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