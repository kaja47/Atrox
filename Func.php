<?php

namespace Atrox;


/** High level functional kung-fu
 *  Warning: might cause headache, nausea, madness and epiphany */
final class Func
{
  use FuncLike, BooleanCombinators;

  const REQUIRED = 'required';
  const OPTIONAL = 'optional';
  const VARARGS  = PHP_INT_MAX;

  /** @var callable */
  private $f;

  private function __construct($f) { $this->f = $f; }
  static function make($f) { return new self($f); }

  // requirement of trait FuncLike 
  protected function func() { return $this->f; }
  protected function makeFunc($f) { return self::make($f); }


  static function curry($f, $argsCount) {
    return self::make($f)->curried($argsCount);
  }


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

}
