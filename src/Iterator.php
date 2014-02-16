<?php //5.4+

namespace Atrox;

require_once __DIR__.'/GenLike.php';

trait IteratorLike {
  use GenLike;

  function make($gen) { return new Iterator($gen); }

  function filter($p)  { return new FilteredIterator($this->iter(), $p); }
  function slice($offset, $count) { return new SlicedIterator($this->iter(), $offset, $count); }

  function append(\Iterator $iter) {
    return new Iterator(Gen::flatten(array_merge([$this->iter()], func_get_args())));
  }

  function toArray() { return iterator_to_array($this->iter()); }
}

trait DelegateInnerIterator {
  private $iter;

  function __construct(\Iterator $iter) { $this->iter = $iter; }

  function current() { return $this->iter->current(); }
  function key()     { return $this->iter->key(); }
  function next()    { return $this->iter->next(); }
  function rewind()  { return $this->iter->rewind(); }
  function valid()   { return $this->iter->valid(); }
}


class Iterator implements \Iterator {
  use IteratorLike;
  use DelegateInnerIterator;

  function iter() { return $this->iter; }

  static function of($arr = []) {
    if ($arr instanceof \Closure)
      return new Iterator($arr());

    return new Iterator(Gen::flatten(func_get_args()));
  }

  static function zero() {
    return new EmptyIterator;
  }
}

class FilteredIterator extends \CallbackFilterIterator { use IteratorLike; }
class SlicedIterator extends \LimitIterator { use IteratorLike; }
class EmptyIterator extends \EmptyIterator { use IteratorLike; }
