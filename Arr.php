<?php

namespace Atrox;

/** Arr - proper functional collection */
class Arr implements \IteratorAggregate, \ArrayAccess, \Countable {

  private $arr;
  private $hasDefault = false;
  private $default = null;

  function __construct(array $arr){ $this->arr = $arr; }
  static function from($arr)  { return new self($arr); }
  static function make()      { return new self(func_get_args()); }
  static function combine($keys, $vals) { return new self(array_combine($keys, $vals)); }
  static function range($start, $end, $step = 1) { return new self(range($start, $end, $step)); }

  function withDefault($f)    { $a = new Arr($this->arr); $a->hasDefault = true; $a->default = $f; return $a; }
  function withDefaultValue($val) { return $this->withDefault(function($k) use($val) { return $val; }); }

  function getIterator()      { return new \ArrayIterator($this->arr); }

  function offsetExists($k)   { return array_key_exists($k, $this->toArray()); }
  function offsetGet($k)      { if (array_key_exists($k, $this->arr)) return $this->arr[$k];
                                if ($this->hasDefault) { $f = $this->default; return $f($k); }
                                return $this->arr[$k]; }
  function offsetSet($k, $v)  { throw new \BadMethodException('This is functional collection, you cannot fuck around like lunatic.'); }
  function offsetUnset($k)    { throw new \BadMethodException('This is functional collection, you cannot fuck around like lunatic.'); }

  function __invoke($k)       { return $this->offsetGet($k); }

  function getOrElse($k, $d)  { if ($this->offsetExists($k)) return $this->offsetGet($k); return $d; }

  function getKeys()          { return new Arr(array_keys($this->toArray())); }
  function getValues()        { return new Arr(array_values($this->toArray())); }

  function contains($el)       { return array_search($el, $this->toArray(), true) !== false; }
  function containsSlice($els){ return $this->indexOfSlice($els, 0) != -1; }
  function containsKey($k)    { return $this->offsetExists($k); }
  function corresponds($coll, $f) { $it = $this->getIterator(); $it->rewind(); foreach ($coll as $ck => $cv) { if (!$it->valid() || !$f($it->current(), $cv)) return false; $it->next(); } return !$it->valid();   }
  function endsWith($coll)    { return $this->startsWith($coll, $this->count() - count($coll)); }
  function exists($f)         { foreach ($this as $k => $v) { if ($f($v)) return true; } return false; }
  function find($p)           { foreach ($this as $k => $v) { if ($p($v)) return $v; } return null; }
  function fold($el, $op)     { return $this->foldLeft($el, $op); }
  function foldLeft($el, $op) { return array_reduce($this->toArray(), $op, $el); }                       // $op(sum, x)
  function foldRight($el, $op){ foreach ($this->reverse() as $k => $v) $el = $op($v, $el); return $el; } // $op(x, sum)
  function forall($f)         { foreach ($this as $k => $v) { if (!$f($v)) return false; } return true; }
  function indexOf($el, $from = 0){ if ($from > 0) return $this->drop($from)->indexOf($el); return array_search($el, $this->toArray(), true); }
  function indexOfSlice($els, $from = 0) { $self = $this->drop($from); while($self->nonEmpty()) { if ($self->startsWith($els)) return $from; $from++; $self = $self->tail(); } return -1;  }
  function indexWhere($f, $from = 0) { foreach ($this->drop($from) as $k => $v) if ($f($v)) return $k; return -1; }
  function isEmpty()          { return count($this) === 0; }
  function lastIndexOf($el, $end = null) { return $this->lastIndexWhere(function ($e) use($el) { return $e === $el; }, $end); }
//  function lastIndexOfSlice($els, $end = null) { if ($end === null) $end = count($this); $self = $this->take($end); while($self->nonEmpty()) { if ($self->endsWith($els)) return $end - count($els); $end--; $self = $self->init(); } return -1;  }
  function lastIndexWhere($f, $end = null) { return $this->take($end)->reverse()->indexWhere($f);  }
  function mkString($sep = ''){ $args = func_get_args(); list($start, $sep, $end) = (count($args) === 3) ? $args : array("", $sep, ""); return $start.implode($sep, $this->toArray()).$end; }
  function nonEmpty()         { return !$this->isEmpty(); }
  function prefixLength($f)   { return $this->segmentLength($f, 0); }
  function randomElement()    { return $this[$this->randomKey()]; }
  function randomKey()        { return array_rand($this->toArray()); }
  function reduce($op)        { return $this->reduceLeft($op); }
  function reduceLeft($op)    { if ($this->isEmpty()) { throw new \InvalidArgumentException("Cannot reduce empty collection"); } return $this->tail()->foldLeft($this->head(), $op); }
  function reduceRight($op)   { if ($this->isEmpty()) { throw new \InvalidArgumentException("Cannot reduce empty collection"); } return $this->init()->foldRight($this->last(), $op); }
  function sameElements($coll){ return $this->corresponds($coll, function ($a, $b) { return $a === $b; }); }
  function segmentLength($f, $from = 0) { $i = 0; $it = $this->drop($from); foreach ($it as $k => $v) { if (!$f($v)) break; $i++; } return $i; }
  function startsWith($coll, $from = 0)  { $it = $this->drop($from)->getIterator(); $it->rewind(); foreach ($coll as $k => $v) { if ($it->valid() && $v === $it->current()) $it->next(); else return false; } return true; }

  function count($f = null)   { if ($f === null) return count($this->toArray()); $c = 0; foreach ($this as $k => $v) { if ($f($v)) $c++; } return $c; }
  function countValues()      { return new Arr(array_count_values($this->toArray())); }
  function size()             { return $this->count(); }
  function max()              { return max($this->toArray()); }
  function min()              { return min($this->toArray()); }
  function maxBy($f)          { $max = $this->map($f)->max(); return $this->find(function ($v) use($max, $f) { return $f($v) === $max; }); }
  function minBy($f)          { $min = $this->map($f)->min(); return $this->find(function ($v) use($min, $f) { return $f($v) === $min; }); }
  function product()          { return array_product($this->toArray()); }
  function sum()              { return array_sum($this->toArray()); }

  function head()             { return reset($this->arr); }
  function last()             { return end($this->arr); }
  function init()             { return $this->slice(0, -1); }
  function tail()             { return $this->drop(1); }
  function inits()            { $res = array($this); $col = $this; while ($col->nonEmpty()) { $res[] = $col = $col->init(); } return new Arr($res); }
  function tails()            { $res = array($this); $col = $this; while ($col->nonEmpty()) { $res[] = $col = $col->tail(); } return new Arr($res); }

  function collect($f)        { return $this->map($f)->filter(); }
  function distinct($sortFlag = SORT_REGULAR) { return new Arr(array_unique($this->toArray(), $sortFlag)); }
  function drop($c)           { return $this->slice($c, $this->count()); }
  function dropRight($c)      { return $this->slice(0, $this->count() - $c); }
  function dropWhile($f)      { $res = array(); $go = false; foreach ($this as $k => $v) { if (!$f($v)) $go = true; if ($go) $res[$k] = $v; } return new Arr($res); }
  function filter($f = null)  { return new Arr(array_filter($this->toArray(), $f)); }
  function filterKeys($f)     { $res = array(); foreach ($this as $k => $v) { if ($f($k)) $res[$k] = $v; } return new Arr($res); }
  function filterNot($f)      { return $this->filter(function ($x) { return !$f($x); }); }
  function flatMap($f)        { return $this->map($f)->flatten(); }
  function flatten()          { $res = array(); foreach ($this as $k => $vs) foreach ($vs as $k => $v) $res[] = $v; return new Arr($res);  }
  function flip()             { return new Arr(array_flip($this->toArray())); }
  function doForeach($f)      { foreach ($this as $k => $v) { $f($v); } }
  function groupBy($f)        { $res = array(); foreach ($this as $k => $v) { $key = $f($v); $res[$key][$k] = $v; } $r = new Arr($res); return $r->map(function ($m) { return new Arr($m); }); }
  function grouped($size)     { $res = new Arr(array_chunk($this->toArray(), $size, true)); return $res->map(function ($e) { return new Arr($e); }); }
  function map($f)            { return new Arr(array_map($f, $this->toArray())); }
  function mapPairs($f)       { return $this->remap(function ($k, $v) use($f) { return array($k => $f($k, $v)); }); }
  function remap($f)          { $res = array(); foreach ($this as $k => $v) foreach ($f($k, $v) as $nk => $nv) $res[$nk] = $nv; return new Arr($res); }
  function padTo($len, $el)   { return new Arr(array_pad($this->toArray(), $len, $el)); }
  function partition($f)      { $a = $b = array(); foreach ($this as $k => $v) { if ($f($v)) $a[$k] = $v; else $b[$k] = $v; } return array(new static($a), new static($b)); }
  function reverse()          { return new Arr(array_reverse($this->toArray(), true)); }
  function scanLeft($el, $op) { $res = array(); $res[] = $el; foreach ($this as $k => $v) { $res[] = $el = $op($el, $v); } return new Arr($res); } // $op(sum, el)
  function scanRight($el, $op){ $res = array(); $res[] = $el; foreach ($this->reverse() as $k => $v) { $el = $op($v, $el); array_unshift($res, $el); } return new Arr($res); } // $op(el, sum)
  function slice($from, $len = null) { return new Arr(array_slice($this->toArray(), $from, $len, true)); }
  function sliding($size, $step = 1) { $res = array(); for ($i = 0; $i <= $this->count() - $size + $step - 1; $i += $step) $res[] = $this->slice($i, $size); return new Arr($res); }
  // *** sort, rsort, asort, arsort, ksort, krsort, usort, uasort, uksort // so many function, wat do?
  private function _sort($func, $arg = null){ $arr = $this->toArray(); if ($arg === null) $func($arr); else $func($arr, $arg); return new Arr($arr); }
//  function sortBy($f)          {  }
  function sortWith($cmpf)    { return $this->_sort('uasort', $cmpf); }
  function sortKeysWith($cmpf){ return $this->_sort('uksort', $cmpf); }
  function sort()             { return $this->_sort('asort'); }
  function sortKeys()         { return $this->_sort('ksort'); }
  function span($f)           { $l = $r = array(); $toLeft = true; foreach ($this as $k => $v) { $toLeft = $toLeft && $f($v); if ($toLeft) $l[$k] = $v; else $r[$k] = $v; } return array(new Arr($l), new Arr($r)); }
  function take($c)           { return $this->slice(0, $c); }
  function takeRight($c)      { return $this->slice(-$c, $c); }
  function takeWhile($f)      { $res = array(); foreach ($this as $k => $v) { if (!$f($v)) break; $res[$k] = $v; } return new Arr($res); }
  function transpose()        { $arr = $this->toArray(); array_unshift($arr, null); $arr = call_user_func_array('array_map', $arr); foreach ($arr as $k => $v) $arr[$k] = (array)$v; return new Arr($arr); }
  function toArray()          { return $this->arr; }
  function toArrayRecursive() { $res = $this->arr; foreach ($res as $k => $v) { $res[$k] = ($v instanceof self ? $v->toArray() : $v); } return $res; }
  function toArrayObject()    { return new \ArrayObject($this->arr);  }
//  function unzip()            {  }
//  function zip($col)          {  }
//  function zipWithIndex()     { $res = array(); foreach ($this as $k => $v) $res[] = $v; new Arr($res); }

  function append($el)        { return $this->appendAll(func_get_args()); }
  function appendAll($els)    { $res = $this->toArray(); foreach ($els as $v) $res[] = $v; return new Arr($res); }
  function prepend($el)       { return $this->prependAll(func_get_args()); }
  function prependAll($els)   { foreach ($this as $v) $els[] = $v; return new Arr($els); }
  function updated($replace)  { $args = func_get_args(); if (count($args) === 2) $replace = array($args[0] => $args[1]); $arr = $this->toArray(); $arr = array_replace($arr, $replace); return new Arr($arr); }
  function patch($from, $patch, $replaced = 0) { $res = $this->toArray(); array_splice($res, $from, $replaced, $patch); return new Arr($res); }

  // todo: diff, merge, intersect
}
