# The Serializer Component
This component is for serialize and deserialize.

Actually provided types
- JSON
    - method: deserialize

1. [Install](#install)
2. [Examples](#examples)

<a name="install"></a>
```
compose require terrazza/component-serializer
``` 

<a name="examples"></a>

## Deserialize JSON
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