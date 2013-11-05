Atrox tools
===========

Atrox\Func
----------

`Func` is function object that contains some useful methods to combine and compose functions.

This code:

```php
Func::make($f)->compose($g)->compose($h)
```

returns function (Func object to be precise) equivalent to:

```php
Func::make(function ($arg) {
  return $h($g($f($arg)));
});
```

Method `andThen` is reverse composition, so this:

```php
Func::make($f)->andThen($g)->andThen($h);
```
produce something like this:

```php
Func::make(function ($arg) {
  return $f($g($h($arg)));
});
```

There are more combinators and utility functions.

* `Func::lazy($f)` lazily evaluate function `$f` and then reuses it's value.
* `Func::identity` is identity function.
* `Func::arr($arr)` makes array, collection or string to behave as function from keys to values. 
* `Func::keySet($arr)` makes array or collection to behave as function from keys to boolean.

Last two combinators are usefull for defining function by extension.


Atrox\Arr
---------

Arr is strict functional collection heavily inspired by [Scala collections](http://www.scala-lang.org/files/archive/nightly/docs/library/#scala.collection.Seq).
If you want some documentation you should head there.

Atrox\Gen + Atrox\GenLike + Atrox\Iterator
------------------------------------------

`Gen` is static class providing many combinators for working with [PHP 5.5 generators](http://us2.php.net/manual/en/class.generator.php).
It contains same operations as `Arr`.

`GenLike` is trait that delegate all `Gen`'s static methods to object.
`Iterator` is one of its applications - rich iterator with all `GenLikes`
mothods mixed in.  Because all this methods use generators under the hood, they
act as iterators that lazily iterate through underlying collection (that might
be itself lazy view or iterator). If you chain `GenLikes`'s methods you are not
allocating new collections along the way, but you are only creaing new
generator/iterator that will be eventualy iterated through and all
transformations takes place.

```php
$it = Iterator::of($array)->map($f)->filter($p)->take(1000);

foreach ($it as $k => $v) {
  // only now is $it actualy iterated
  // generating first thousand values
  // mapped by function $f that holds
  // predicate $p
}
```

Atrox\Access
------------

`Access` is quite powerful beast improving PHP function syntax.

If you did some functional-like programming in this damned language you writen something like this so many times it's not even funny:

```php
array_map(function ($p) { return $p->age; }, $people);
```

But with `Access` you can replace it by something that looks remarkably like Scala function syntax.

```php
$_ = new Access;
array_map($_->age, $people);
```

And of course you can nest these to your heart's desire:

```php
array_map(function ($x) { return $x->y['z']->zzz(1); }, $xs);
```

```php
$_ = new Access;
array_map($_->y['z']->zzz(1), $xs);
```


// todo: make somebody to correct this abomination
