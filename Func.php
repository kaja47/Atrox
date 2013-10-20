<?php

namespace Atrox;

/** High level functional kung-fu
 *  Warning: might cause headache, nausea, madness and epiphany */
final class Func
{
  const REQUIRED = 'required';
  const OPTIONAL = 'optional';
  const VARARGS  = PHP_INT_MAX;

  /** @var callable */
  private $f;

  private function __construct($f) { $this->f = $f; }
  static function make($f) { return new self($f); }

  /** chain([$fa, $fb, $fc, $fd]) -> $fa($fb($fc($fd))) */
  static function chain($fs) { return self::make(function ($x) use($fs) { return array_reduce($fs, function ($x, $f) { return $f($x); }, $x); }); }

  /** function passed as argument is executed only once, every subsequent call reuse this result */
  static function lazy($f) {
    $isSet = $value = false;
    return function() use(&$isSet, &$value, $f) {
      if (!$isSet) {
        $value = $f();
        $isSet = true;
      }
      return $value;
    };
  }

  static function identity($id) { return $id; }


  /** Make arrays, strings, collections (or anything that can be accesed as
    * array) act as function from keys to values */
  static function arr($arr) {
    return self::make(function ($k) use ($arr) {
      return $arr[$k];
    });
  }

  /** Make arrays, strings, collections (or anything that can be accesed as
      * array) act as function that test whether key is present. */
  static function keySet($arr) {
    if (is_array($arr)) {
      return self::make(function ($k) use ($arr) {
        return array_key_exists($k, $arr);
      });
    } else {
      return self::make(function ($k) use ($arr) {
        return isset($arr[$k]);
      });
    }
  }


  /** $f($a, $b, $c) --[curried]-> $f($a)($b)($c) */
  function curried($argsCount = 'required', $args = []) {
    if ($argsCount === self::REQUIRED || $argsCount === self::OPTIONAL) {
      $f = $this->f;
      $refl = is_array($f) ? new \ReflectionMethod($f[0], $f[1]) : new \ReflectionFunction($f);
      $argsCount = ($argsCount === self::REQUIRED) ? $refl->getNumberOfRequiredParameters() : $refl->getNumberOfParameters();
    }

    $self = $this;
    return self::make(function () use($argsCount, $args, $self) { 
      $args = array_merge($args, func_get_args());
      if (count($args) >= $argsCount || func_num_args() === 0) 
        return call_user_func_array($self->f, $args);
      return $self->curried($argsCount, $args);
    });
  }

  /** $f($a)($b)($c) -> $f($a, $b, $c) */
  function uncurried() { $f = $this->f; return self::make(function () use($f) { foreach (func_get_args() as $arg) { $f = $f($arg); } return $f; }); }

  /** $this($a, $b, $c) --[tupled]--> $this(array($a, $b, $c)) */
  function tupled()    { $f = $this->f; return self::make(function($args) use($f) { return call_user_func_array($f, $args); }); }
	
  /** $this(array($a, $b, $c)) --[untupled]--> $this($a, $b, $c) -> */
  function untupled()  { $f = $this->f; return self::make(function() use($f) { return call_user_func_array($f, [func_get_args()]); }); }

  /** $this->andThen($f)($_) -> $f($this($_)) */
  function andThen($g) { $f = $this->f; return self::make(function() use($f, $g) { return $g(call_user_func_array($f, func_get_args())); }); }

  /** $this->compose($f)($_) -> $this($f($_)) */
  function compose($g) { $f = $this->f; return self::make(function() use($f, $g) { return $f(call_user_func_array($g, func_get_args())); }); }


  function __invoke()   { return call_user_func_array($this->f, func_get_args()); }
  function invoke()     { return call_user_func_array($this->f, func_get_args()); }
  function invokeArgs(array $args) { return call_user_func_array($this->f, $args); }
  function isCallable() { return is_callable($this->f); }
  function getNative()  { return $this->f; }

  function _and($g) { $f = $this->f; return self::make(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) && call_user_func_array($g, func_get_args()); }); }
  function _or($g)  { $f = $this->f; return self::make(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) || call_user_func_array($g, func_get_args()); }); }
  function _xor($g) { $f = $this->f; return self::make(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) ^  call_user_func_array($g, func_get_args()); }); }
  function not()    { $f = $this->f; return self::make(function($arg) use($f)     { return !call_user_func_array($f, func_get_args()); }); }

  function __call($name, $args) {
    if (in_array($name, ['and', 'or', 'xor'])) return $this->{"_$name"}(reset($args));
    trigger_error("Call to undefined method Atrox\\Func::$name()", E_USER_ERROR);
  }
}
