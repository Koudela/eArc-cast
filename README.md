# eArc-cast

earc/cast is a lightweight zero dependencies flat data mapper to cast data represented 
by objects/arrays onto other objects/arrays. Thus helping to implement the adapter 
pattern in nearly no time.

It uses property reflection to access the objects and supports user defined mappings. 

earc/cast can be used everywhere without dependency injection even in vanilla functions.

## table of contents

- [install](#install)
- [bootstrap](#bootstrap)
- [basic usage](#basic-usage)
    - [array to array](#array-to-array)
    - [array to object](#array-to-object)
    - [object to array](#object-to-array)
    - [object to object](#object-to-object)
    - [reverse casting](#reverse-casting)
    - [generate mapping](#generate-mapping)
- [advanced usage](#advanced-usage)
- [architectural considerations](#architectural-considerations)
- [releases](#releases)
    - [release 0.0](#release-00)
    - [release 1.0](#release-10)
    - [release 1.1](#release-11)
  
## install

```bash
$ composer require earc/cast
```

## bootstrap

Place the following code snippets in the section where your script/framework is 
bootstrapped.

1. Make use of the composer namespace driven autoloading.

```php
require_once '/path/to/your/vendor/autoload.php';
```

2. Then bootstrap earc/cast to declare the `cast`, `cast_simple` and `cast_reverse` 
   functions.

```php
use eArc\Cast\Initializer;

Initializer::init();
```

## basic usage

earc/cast supports four types of casting: [array to array](#array-to-array), [array to object](#array-to-object), 
[object to array](#object-to-array) and [object to object](#object-to-object).

### array to array

If the mapping is omitted, values with identical keys (property names) are assigned from
the origin to the target.

```php
use function eArc\Cast\cast_simple;

$origin = [10, 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];
$target = [1, 2, 3, 'd' => 'X'];

$result = cast_simple($origin, $target);

// $result = [10, 2, 3, 'd' => 'D']
```

In the case of a supplied mapping the keys of the mapping are used for the origin,
and the values are used for the target. 

Hint: If the value is null, the key is used as value.
```php
use function eArc\Cast\cast_simple;

$origin = [10, 'a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'];
$target = [1, 2, 3, 'd' => 'X'];
$mapping = ['a' => null, 'c' => 'c', 'b' => 'd'];

$result = cast_simple($origin, $target, $mapping);

// $result = [1, 2, 3, 'a' => 'A', 'd' => 'B', 'c' => 'C'];
```

Only if a mapping is supplied not existing keys will be created in the target array.

Hint: If a mapping is supplied identical keys (property names) that are not present
in the mapping are not used to cast values.

### array to object

If the target is an object you can either supply a real object or a class name.
In the case of a class name the object is created via reflection without calling
the constructor.

```php
use function eArc\Cast\cast_simple;
class MyTargetsParent
{
    public string $a = 'parent_A';
    protected string $b = 'parent_B';
    private string $c = 'parent_C';
    public string $d = 'parent_D';
    protected string $e = 'parent_E';
    private string $f = 'parent_F';
}
class MyTarget extends MyTargetsParent
{
    public function __construct() {
        $this->b = 'constructed_B';
    }
    public string $a = 'A';
    protected string $b = 'B';
    private string $c = 'C';
}

$origin = ['a' => 'array_A', 'c' => 'array_C', 'e' => 'array_E', 'f' => 'array_F'];
$target = new MyTarget();

$result = cast_simple($origin, $target);

// result:
// MyTargetsParent...
//    public string $a = 'array_A'; <-- cast
//    protected string $b = 'constructed_B'; <-- normal inheritance
//    private string $c = 'array_C'; <-- cast 
//    public string $d = 'parent_D';
//    protected string $e = 'array_E'; <-- cast
//    private string $f = 'array_F'; <-- cast
// MyTarget...
//    public string $a = 'array_A'; <-- cast
//    protected string $b = 'constructed_B'; <-- constructor is called
//    private string $c = 'array_C'; <-- cast

$result = cast_simple($origin, MyTarget::class);

// result:
// MyTargetsParent...
//    ...
//    protected string $b = 'B'; <-- normal inheritance
//    ... 
// MyTarget...
//    ...
//    protected string $b = 'B'; <-- constructor was not called
//    ...
```

### object to array

If there is an overwritten private property in the origin object, the property
that is nearest to the current class is used.

```php
use function eArc\Cast\cast_simple;
class MyOriginsParent
{
    public string $a = 'parent_A';
    protected string $b = 'parent_B';
    private string $c = 'parent_C';
    public string $d = 'parent_D';
    protected string $e = 'parent_E';
    private string $f = 'parent_F';
}
class MyOrigin extends MyOriginsParent
{
    public string $a = 'A';
    protected string $b = 'B';
    private string $c = 'C';
}

$origin = new MyOrigin();
$target = ['a' => null, 'b' => null, 'c' => null, 'd' => null, 'e' => null, 'f' => null];

$result = cast_simple($origin, $target);

// $result = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'parent_D', 'e' => 'parent_E', 'f' => 'parent_F']
```

### object to object

All that was said about objects in the [array to object](#array-to-object) and 
[object to array](#object-to-array) cases applies.

### reverse casting

Sometimes you need to map some properties apply a service or two and map the object
back to the original data representation. 

If the target is an object, you can use reverse casting to keep track of the origin 
and the mapping. All you have to do is use `cast` instead of `simple_cast`.

```php
use function eArc\Cast\cast;
use function eArc\Cast\cast_reverse;

cast($origin, $target, $mapping);

//.. do something with the target;

$origin = cast_reverse($target);
```

Hint: If you call `cast` multiple times with the **same** target object, the 
origin and mapping of the last call is used by `cast_reverse`.

### generate mapping

A common use case is to use earc/cast to generate a json representation for
a frontend component using all public, protected and private properties. 
Unfortunately earc/cast uses the target to auto generate a mapping, thus casting 
an object to an empty array leaves the array empty. The `generate_mapping` function 
is the solution.

```php
use function eArc\Cast\cast_simple;
use function eArc\Cast\generate_mapping;

return json_encode(cast_simple($object, [], generate_mapping($object)));
```

## advanced usage

To overwrite oder rewrite any logic, you have to pass a class implementing 
the `CastServiceInterface` to the `Initializer`. You may extend the original
CastService.

```php
use eArc\Cast\CastService;
use eArc\Cast\Initializer;

class MyCastService extends CastService
{
    // extend/rewrite logic
}

Initializer::init(new MyCastService());
```

## architectural considerations

If you follow the dependency inversion principle in your software design, application
modules does not depend directly on one another, but on abstractions. This is better
than hard coupled components but leaves the problem that the modules have to know
and use the same abstraction. You cannot develop components independently, and you
cannot reuse components freely.

Think of a slider in a shop that slides product boxes. Let the slider be a part
of a content management system that integrates in a shop platform. Then the product box
slider introduces a dependency of the content management system from the product 
selling platform of the shop. That is why you cannot evolve the two big structures 
cms and product selling platform independently of another. If you cannot break
big structures, you should not even consider breaking small parts into independent
applications. You end up with a monolithic architecture and thousands of (abstract)
dependencies right through the whole application. A nightmare to evolve and maintain.

earc/cast provides an easy way to implement the adapter pattern. This gives you
the freedom that comes with runtime interface polymorphism. The product box slider
can use a different interface for the product than the selling platform. All that
matters is the underlying data. If the product data of the selling platform is 
sufficient to the product box sliders product interface, an adapter can be written
and thus it can be mapped. Now we have true decoupling and can evolve and maintain
the cms independently of the selling platform. Moreover, you can break these two 
applications into smaller parts. All that has to be kept in sync is the adapter 
which is reduced in the case of earc/cast to a simple one-dimensional array (mapping).

## releases

### release 1.1

* new function `generate_mapping`
* Documentation of all features

### release 1.0
* PHP >= 8.0

### release 0.0
* PHP >=5.6
