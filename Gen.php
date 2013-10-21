<?php

namespace Atrox;

class Gen {

  static function of($gen) {
    foreach ($gen as $k => $v)
      yield $k => $v;
  }

  static function map($gen, $f) {
    foreach ($gen as $k => $v)
      yield $k => $f($v);
  }

  static function flatten($gen) {
    foreach ($gen as $xs)
      foreach ($xs as $x)
        yield $x;
  }

  static function flatMap($gen, $f) {
    foreach ($gen as $x)
      foreach ($f($x) as $y)
        yield $y;
  }

  static function filter($gen, $p) {
    foreach ($gen as $k => $v)
      if ($p($v))
        yield $k => $v;
  }

  static function filterNot($gen, $p) {
    foreach ($gen as $k => $v)
      if (!$p($v))
        yield $k => $v;
  }

  static function filterKeys($gen, $p) {
    foreach ($gen as $k => $v)
      if ($p($k))
        yield $k => $v;
  }

  static function drop($gen, $n) {
    return self::slice($gen, $n, PHP_INT_MAX);
  }

  static function dropWhile($gen, $p) {
    $go = false;
    foreach ($gen as $k => $v) {
      if (!$go && !$p($v)) $go = true;
      if ($go) yield $k => $v;
    }
  }

  static function grouped($gen, $n) {
    $gr = [];
    foreach ($gen as $k => $v) {
      $gr[] = $v; // should preserver keys?
      if (count($gr) == $n) {
        yield $gr;
        $gr = [];
      }
    }
    if ($gr)
      yield $gr;
  }

  static function indices($gen) {
    foreach ($gen as $k => $v)
      yield $k;
  }

  static function values($gen) {
    foreach ($gen as $k => $v)
      yield $v;
  }

  static function padTo($gen, $len, $el)  {
    $i = 0;
    foreach ($gen as $k => $v) {
      yield $k => $v;
      $i ++;
    }

    for (; $i < $len; $i++)
      yield $el;
  }

  static function slice($gen, $from, $until) {
    $i = 0;
    foreach ($gen as $k => $v) {
      if ($i >= $until)
        return;

      if ($i >= $from)
        yield $k => $v;

      $i ++;
    }
  }

  static function sliding($gen, $size, $step = 1) {
    $yieldRest = true;
    $window = [];
    foreach ($gen as $k => $v) {
      $window[] = $v; // should preserver keys?
      $yieldRest = true;
      if (count($window) === $size) {
        yield $window;
        $yieldRest = false;
        for ($i = 0; $i < $step; $i++)
          array_shift($window);
      }
    }
    if ($window && $yieldRest)
      yield $window;
  }

  static function take($gen, $n) {
    return self::slice($gen, 0, $n);
  }

  static function takeWhile($gen, $p) {
    foreach ($gen as $k => $v) {
      if (!$p($v)) break;
      yield $k => $v;
    }
  }

  static function zip($genA, $genB, $f = null) {
    $genB = self::of($genB);
    foreach ($genA as $ka => $va) {
      if (!$genB->valid()) return;

      $vb = $genB->current();
      yield $f === null ? [$va, $vb] : $f($va, $vb);

      $genB->next();
    }
  }

  // ***

  static function forall($gen, $p) {
    foreach ($gen as $v) {
      if (!$p($v)) return FALSE;
    }
    return TRUE;
  }

  static function exists($gen, $p) {
    foreach ($gen as $v) {
      if ($p($v)) return TRUE;
    }
    return FALSE;
  }

  static function corresponds($genA, $genB, $p) {
    return Gen::forall(Gen::zip($genA, $genB, $p), static function ($x) { return $x === true; });
  }

  static function count($gen, $p) {
    $c = 0;
    foreach ($gen as $k => $v)
      if ($p($v)) $c++;
    return $c;
  }

  static function find($gen, $p) {
    foreach ($gen as $k => $v)
      if ($p($v)) return $v;
  }

  static function foldLeft($gen, $init, $op) { // op: (sum, el) => sum
    foreach ($gen as $k => $v) {
      $init = $op($init, $v);
    }
    return $init;
  }

}
