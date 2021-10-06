# The Serializer Component
This component is for serialize and deserialize.

Actually provided types
- JSON
    - method: deserialize

1. [How to install](#install)
2. [Examples](#examples)
   - [Json](#examples-json)

<a name="install"></a>
### How to install
#### install via composer
```
compose require terrazza/component-serializer
``` 

<a name="examples"/></a>
### Examples

<a name="examples-json"></a>
#### deserialize JSON
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