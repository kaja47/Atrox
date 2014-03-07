<?php

namespace Atrox;


abstract class Semigroup {
  abstract function plus($a, $b);
}


abstract class Monoid extends Semigroup {
  abstract function zero();

  function sum($xs) {
    $sum = $this->zero();
    foreach ($xs as $x) {
      $sum = $this->plus($sum, $x);
    }
    return $sum;
  }

  static function make($zero, $plus) {
    return new ReadyMadeMonoid($zero, $plus);
  }

  static function num() {
    return new ReadyMadeMonoid(0, function ($a, $b) { return $a + $b; });
  }

  static function max() {
    return new ReadyMadeMonoid(-INF, 'max');
  }

  static function min() {
    return new ReadyMadeMonoid(INF, 'min');
  }

  static function str() {
    return new ReadyMadeMonoid('', function ($a, $b) { return $a . $b; });
  }

  static function map(Monoid $m) {
    return new ReadyMadeMonoid([], function (array $a, array $b) use ($m) {
      foreach ($b as $k => $_) {
        if (array_key_exists($k, $a)) {
          $a[$k] = $m->plus($a[$k], $b[$k]);
        } else {
          $a[$k] = $b[$k];
        }
      }
      return $a;
    });
  }

  static function tuple(array $ms) {
    $zero = array_map(function ($m) { return $m->zero(); }, $ms);
    return new ReadyMadeMonoid($zero, function (array $a, array $b) use ($ms) {
      foreach ($b as $k => $_) {
        $a[$k] = $ms[$k]->plus($a[$k], $b[$k]);
      }
      return $a;
    });
  }
}


final class ReadyMadeMonoid extends Monoid {
  private $zero, $plus; 

  function __construct($zero, $plus) {
    $this->zero = $zero;
    $this->plus = $plus;
  }

  function zero() {
    return $this->zero;
  }

  function plus($a, $b) {
    return call_user_func($this->plus, $a, $b);
  }
}
