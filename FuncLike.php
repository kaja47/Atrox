<?php

namespace Atrox;


trait FuncLike {

  protected function func() { return $this; }
  protected function makeFunc($f) { return Func::make($f); }

  /**
   * $f = function ($a, $b, $c) { ... }
   * $c = $f->curried();
   * $f($a, $b, $c) === $c($a, $b, $c) === $c($a)($b)($c) === $c($a, $b)($c) === $c($a)($b, $c)
   */
  function curried($argsCount = 'required', $args = []) {
    if ($argsCount === Func::REQUIRED || $argsCount === Func::OPTIONAL) {
      $f = $this->func();
      $r = is_array($f) ? new \ReflectionMethod($f[0], $f[1]) : new \ReflectionFunction($f);
      $argsCount = ($argsCount === Func::REQUIRED) ? $r->getNumberOfRequiredParameters() : $r->getNumberOfParameters();
    }

    return $this->makeFunc(function () use ($argsCount, $args) { 
      $args = array_merge($args, func_get_args());
      if (count($args) >= $argsCount || func_num_args() === 0) 
        return call_user_func_array($this->func(), $args);
      return $this->curried($argsCount, $args);
    });
  }

  /** $f($a)($b)($c) -> $f($a, $b, $c) */
  function uncurried() { $f = $this->func(); return $this->makeFunc(function () use($f) { foreach (func_get_args() as $arg) { $f = $f($arg); } return $f; }); }

  /** $this($a, $b, $c) --[tupled]--> $this(array($a, $b, $c)) */
  function tupled()    { $f = $this->func(); return $this->makeFunc(function($args) use($f) { return call_user_func_array($f, $args); }); }
	
  /** $this(array($a, $b, $c)) --[untupled]--> $this($a, $b, $c) -> */
  function untupled()  { $f = $this->func(); return $this->makeFunc(function() use($f) { return call_user_func_array($f, [func_get_args()]); }); }

  /** $this->andThen($f)($_) -> $f($this($_)) */
  function andThen($g) { $f = $this->func(); return $this->makeFunc(function() use($f, $g) { return $g(call_user_func_array($f, func_get_args())); }); }

  /** $this->compose($f)($_) -> $this($f($_)) */
  function compose($g) { $f = $this->func(); return $this->makeFunc(function() use($f, $g) { return $f(call_user_func_array($g, func_get_args())); }); }


  function __invoke()   { return call_user_func_array($this->func(), func_get_args()); }
  function invoke()     { return call_user_func_array($this->func(), func_get_args()); }
  function invokeArgs(array $args) { return call_user_func_array($this->func(), $args); }
  function isCallable() { return is_callable($this->func()); }
  function getNative()  { return $this->func(); }

}
