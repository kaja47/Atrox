<?php

namespace Atrox;


/**
 * Separate trait, because it must define __call method that can have 
 * destructive potential in classes in which it's mixed in
 */
trait BooleanCombinators {
  function _and($g) { $f = $this->func(); return $this->makeFunc(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) && call_user_func_array($g, func_get_args()); }); }
  function _or($g)  { $f = $this->func(); return $this->makeFunc(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) || call_user_func_array($g, func_get_args()); }); }
  function _xor($g) { $f = $this->func(); return $this->makeFunc(function($arg) use($f, $g) { return  call_user_func_array($f, func_get_args()) ^  call_user_func_array($g, func_get_args()); }); }
  function not()    { $f = $this->func(); return $this->makeFunc(function($arg) use($f)     { return !call_user_func_array($f, func_get_args()); }); }

  function __call($name, $args) {
    if (in_array($name, ['and', 'or', 'xor'])) return $this->{"_$name"}(reset($args));
    trigger_error("Call to undefined method Atrox\\Func::$name()", E_USER_ERROR);
  }
}
