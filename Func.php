<?php

namespace Atrox;

use Nette\Object;
use Nette\Reflection;
use Nette\Callback;

/** High level function kung-fu
 *  Warning: might cause madness */
final class Func
{
	const REQUIRED = 'required';
	const OPTIONAL = 'optional';
	const VARARGS  = PHP_INT_MAX;

  private $cb;

  private function __construct(Callback $cb) { $this->cb = $cb; }
  static function make($cb, $m = NULL) { return new self(\callback($cb, $m)); }

  /** chain(array($fa, $fb, $fc, $fd)) -> $fa($fb($fc($fd))) */
  static function chain($fs) { return self::make(function ($x) use($fs) { return array_reduce($fs, function ($x, $f) { return $f($x); }, $x); }); }

  /** $f($a, $b, $c) --[curried]-> $f($a)($b)($c) */
  function curried($argsCount = 'required', $args = array()) {
    if ($argsCount === self::REQUIRED || $argsCount === self::OPTIONAL) {
      $f = $this->cb->getNative();
      $refl = is_array($f) ? new Reflection\Method($f[0], $f[1]) : new Reflection\GlobalFunction($f);
      $argsCount = ($argsCount === self::REQUIRED) ? $refl->getNumberOfRequiredParameters() : $refl->getNumberOfParameters();
    }

    $self = $this;
    return self::make(function () use($argsCount, $args, $self) { 
      $args = array_merge($args, func_get_args());
      if (count($args) >= $argsCount || func_num_args() === 0) 
        return call_user_func_array($self, $args);
      return $self->curried($argsCount, $args);
    });
  }

  /** $f($a)($b)($c) -> $f($a, $b, $c) */
  function uncurried() { $cb = $this->cb; return self::make(function () use($cb) { foreach (func_get_args() as $arg) { $cb = $cb($arg); } return $cb; }); }

  /** $this($a, $b, $c) --[tupled]--> $this(array($a, $b, $c)) */
  function tupled()    { $cb = $this->cb; return self::make(function($args) use($cb) { return call_user_func_array($cb, $args); }); }
	
  /** $this(array($a, $b, $c)) --[untupled]--> $this($a, $b, $c) -> */
  function untupled()  { $cb = $this->cb; return self::make(function() use($cb) { return call_user_func_array($cb, array(func_get_args())); }); }

  /** $this->andThen($f)($_) -> $f($this($_)) */
  function andThen($f) { $cb = $this->cb; return self::make(function() use($cb, $f) { return $f(call_user_func_array($cb, func_get_args())); }); }

  /** $this->compose($f)($_) -> $this($f($_)) */
  function compose($f) { $cb = $this->cb; return self::make(function() use($cb, $f) { return $cb(call_user_func_array($f, func_get_args())); }); }


  function __invoke()   { return call_user_func_array(array($this->cb, '__invoke'), func_get_args()); }
  function invoke()     { return call_user_func_array(array($this->cb, 'invoke'),   func_get_args()); }
  function invokeArgs(array $args) { return $this->cb->invokeArgs($args); }
  function isCallable() { return $this->cb->isCallable(); }
  function getNative()  { return $this->cb->getNative(); }
  function isStatic()   { return $this->cb->isStatic(); }
  function __toString() { return $this->cb->__toString(); }

  function _and($f) { $self = $this; return self::make(function($arg) use($self, $f) { return  call_user_func_array($self, func_get_args()) && call_user_func_array($f, func_get_args()); }); }
  function _or($f)  { $self = $this; return self::make(function($arg) use($self, $f) { return  call_user_func_array($self, func_get_args()) || call_user_func_array($f, func_get_args()); }); }
  function _xor($f) { $self = $this; return self::make(function($arg) use($self, $f) { return  call_user_func_array($self, func_get_args()) ^  call_user_func_array($f, func_get_args()); }); }
  function not()    { $self = $this; return self::make(function($arg) use($self)     { return !call_user_func_array($self, func_get_args()); }); }

  function __call($name, $args) {
    if (in_array($name, array('and', 'or', 'xor'))) return $this->{"_$name"}(reset($args));
    trigger_error("Call to undefined method Atrox\\Func::$name()", E_USER_ERROR);
  }
}
