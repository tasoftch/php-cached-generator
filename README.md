# PHP Utilities
This package contains several utilities I often use.

### Cachable generator
```TASoft\Utility\CachableGenerator```

#### Issue
A generator is a forward only iterator.  
Fetching resources from a file or database or wherever consumes performance. There I love to use generators to only load what is really required.  

    I want to load a user with the username "admin"
    Of course using MySQL you can create a specific request.  
    But If you do not know the sources, you need to iterate over all users and check, if one matches to the username.  
    With common iterators PHP will load all users first and then you have the possibility to pick the right one.  
    Using generators you may load user by user and check. If found, stop iteration.

But what if you want again to look for a user?  
You can not rewind a generator.
```php
<?php
$yieldCount = 0;
$gen = function () use(&$yieldCount) {
    $yieldCount++;
    yield "test-user";

    $yieldCount++;
    yield "admin";

    $yieldCount++;
    yield "normal-user";
    
    $yieldCount++;
    yield "root";
    
    $yieldCount++;
    yield "anonymous";
};

foreach ($gen() as $value) {
    echo "$value\n";
}

/** Output:
test-user
admin
normal-user
root
anonymous
*/

echo $yieldCount; // 5

// searching for admin

$yieldCount = 0;
$user = NULL;

foreach ($gen() as $value) {
    if($value == "admin") {
        $user = $value;
        break;
    }
}
if($user)
    echo "Administrator found at iteration $yieldCount.\n";

// Output: Administrator found at iteration 2.

// Now you need information about the root user:
$yieldCount = 0;
$user = NULL;

foreach ($gen() as $value) {
    if($value == "root") {
        $user = $value;
        break;
    }
}
if($user)
    echo "Administrator found at iteration $yieldCount.\n";

// Output: Administrator found at iteration 4.
// As you see, the generator need to restart.
// Of course, you might continue with the same generator, but if the root user came before admin, you'll never get it.

```
#### Solution: The cachable generator
It is an object that takes a generator and its invocation will forward to the generator.  
But in addition, it caches the yielded values.  
Iterating again, it will start using the cached values and continue yielding from the generator until the generator is not valid anymore.

```php
<?php
use TASoft\Utility\CachedGenerator;

$yieldCount = 0;
$gen = new CachedGenerator(
    (
        function () use(&$yieldCount) {
             $yieldCount++;
             yield "test-user";
         
             $yieldCount++;
             yield "admin";
         
             $yieldCount++;
             yield "normal-user";
             
             $yieldCount++;
             yield "root";
             
             $yieldCount++;
             yield "anonymous";
        }
     ) /* directly call the closure */ ()
);

$yieldCount = 0;
$user = NULL;

foreach ($gen() as $value) {
    if($value == "admin") {
        $user = $value;
        break;
    }
}
if($user)
    echo "Administrator found at iteration $yieldCount.\n";

// Output: Administrator found at iteration 2.
```