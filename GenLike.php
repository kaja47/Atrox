<?php

namespace Atrox;

require_once __DIR__.'/Gen.php';

trait GenLike {

  function make($gen) { return new self($gen); }
  function iter()     { return $this; }

  function map($f)                   { return $this->make(Gen::map($this->iter(), $f)); } 
  function flatten()                 { return $this->make(Gen::flatten($this->iter())); } 
  function flatMap($f)               { return $this->make(Gen::flatMap($this->iter(), $f)); } 
  function filter($p)                { return $this->make(Gen::filter($this->iter(), $p)); } 
  function filterNot($p)             { return $this->make(Gen::filterNot($this->iter(), $p)); } 
  function filterKeys($p)            { return $this->make(Gen::filterKeys($this->iter(), $p)); } 
  function drop($n)                  { return $this->make(Gen::drop($this->iter(), $n)); } 
  function dropWhile($p)             { return $this->make(Gen::dropWhile($this->iter(), $p)); } 
  function grouped($n)               { return $this->make(Gen::grouped($this->iter(), $n)); } 
  function indices()                 { return $this->make(Gen::indices($this->iter())); } 
  function values($gen)              { return $this->make(Gen::values($this->iter())); } 
  function padTo($len, $el)          { return $this->make(Gen::padTo($this->iter(), $len, $el)); } 
  function slice($from, $until)      { return $this->make(Gen::slice($this->iter(), $from, $until)); } 
  function sliding($size, $step = 1) { return $this->make(Gen::sliding($this->iter(), $size, $step)); } 
  function take($n)                  { return $this->make(Gen::take($this->iter(), $n)); } 
  function takeWhile($p)             { return $this->make(Gen::takeWhile($this->iter(), $p)); } 
  function zip($gen, $f = null)      { return $this->make(Gen::zip($this->iter(), $gen, $f)); } 

  // ***

  function forall($p)                { return Gen::forall($this->iter(), $p); }
  function exists($p)                { return Gen::exists($this->iter(), $p); }
  function corresponds($gen, $p)     { return Gen::corresponds($this->iter(), $gen, $p); } 
  function count($p)                 { return Gen::count($this->iter(), $p); }
  function find($p)                  { return Gen::find($this->iter(), $p); }
  function foldLeft($init, $op)      { return Gen::foldLeft($this->iter(), $init, $op); }

}
