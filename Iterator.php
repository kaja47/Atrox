<?php //5.4+

namespace Atrox;

trait IteratorLike {
  function map($f)     { return new MappedIterator($this, $f); }
  function flatMap($f) { return new FlatMappedIterator($this, $f); }
  function filter($p)  { return new FilteredIterator($this, $p); }
  function slice($offset, $count) { return new SlicedIterator($this, $offset, $count); }
  function append(\Iterator $iter) { return new AppendedIterator($this, $iter); }
}

trait DelegrateInnerIterator {
  function current() { return $this->iter->current(); }
  function key()     { return $this->iter->key(); }
  function next()    { return $this->iter->next(); }
  function rewind()  { return $this->iter->rewind(); }
  function valid()   { return $this->iter->valid(); }
}


class Iterator implements \Iterator {
  use IteratorLike;
  use DelegrateInnerIterator;

  private $iter;

  function __construct(\Iterator $iter) { $this->iter = $iter; }

  static function fromArray(array $arr) { return new self(new \ArrayIterator($arr)); }
}

class MappedIterator implements \Iterator {
  use IteratorLike;
  use DelegrateInnerIterator;

  private $iter, $f;

  function __construct(\Iterator $iter, $f) { $this->iter = $iter; $this->f = $f; }

  function current()
  {
    return call_user_func($this->f, $this->iter->current());
  }

}

class FlatMappedIterator implements \Iterator {
  use IteratorLike;
  use DelegrateInnerIterator;

  private $iter;

  function __construct(\Iterator $iter, $f) {
    $is = array_map($f, iterator_to_array($iter)); // fixme: eagerly evaluates passed iterator
    $app = new \AppendIterator;
    foreach ($is as $i) $app->append($i);
    $this->iter = $app;
  }
}

class FilteredIterator extends \CallbackFilterIterator {
  use IteratorLike;
}

class SlicedIterator extends \LimitIterator {
  use IteratorLike;
}

class AppendedIterator implements \Iterator {
  use IteratorLike;
  use DelegrateInnerIterator;

  private $iter;

  function __construct(\Iterator $iter) { $this->iter = new \AppendIterator; foreach (func_get_args() as $i) { $this->iter->append($i); } }
}
