<?php

namespace Atrox;

/**
 * Class for generating accessor functions.
 * Usage: https://gist.github.com/kaja47/5846724
 */
final class Access implements \ArrayAccess {

  private $f;

  function __construct($f = null) {
    $this->f = ($f === null) ? function ($x) { return $x; } : $f;
  }

  function __get($name) {
    return new Access(function ($x) use ($name) {
      return $this($x)->$name;
    });
  }

  function __call($name, $args) {
    return new Access(function ($x) use ($name, $args) {
      return call_user_func_array([$this($x), $name], $args);
    });
  }

  function offsetGet($offset) {
    return new Access(function ($x) use ($offset) {
      return $this($x)[$offset];
    });
  }

  function offsetSet($offset, $val) {
    throw new \BadMethodCallException("unsupported operation");
  }

  function offsetExists($offset) {
    throw new BadMethodCallException("unsupported operation");
  }

  function offsetUnset($offset) {
    throw new BadMethodCallException("unsupported operation");
  }

  /** executes accessor function */
  function __invoke($args) {
    return call_user_func($this->f, $args);
  }
}
