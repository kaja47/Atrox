<?php

namespace Atrox;

require_once __DIR__.'/GenLike.php';

final class LazyArrayList implements \IteratorAggregate, \ArrayAccess, \Countable {
  use GenLike;

  private $list;
  private $gen;

  //function make($gen) { return new LazyArrayList($gen) }
  function iter() { return $this->getIterator(); }


  function __construct($gen) {
    $this->list = new \SPLFixedArray;
    $this->gen = $gen instanceof \Closure ? $gen() : $gen;
  }
  
  private function unwindUpTo($pos) {
    if ($this->gen === null)
      return;

    if ($pos < $this->list->count())
      return;

    $max = $this->list->count();
    for ($i = $max; $i <= $pos; $i++) {
      if (!$this->gen->valid()) {
        $this->gen = null;
        return;
      }
      $this->list->setSize($i+1);
      $this->list[$i] = $this->gen->current();
      $this->gen->next();
    }
  }

  // *** IteratorAggregate

  function getIterator() {
    for ($i = 0; $i < $this->list->count(); $i++)
      yield $this->list[$i];

    for ($i = $this->list->count();; $i++) {
      $this->unwindUpTo($i);
      if ($this->list->count() <= $i)
        return;
      yield $this->list[$i];
    }
  }

  // *** Countable

  function count() {
    $this->unwindUpTo(PHP_INT_MAX-1);
    return $this->list->count();
  }

  // *** ArrayAccess

  function offsetGet($idx) {
    $this->unwindUpTo($idx);
    return $this->list[$idx];
  }

  function offsetExists($idx) {
    $this->unwindUpTo($idx);
    return $this->list->count() > $idx;
  }

  function offsetSet($idx, $val) {
    throw \Exception("not supported");
  }

  function offsetUnset($idx) {
    throw \Exception("not supported");
  }
}
