<?php

require_once __DIR__.'/../Gen.php';

use Atrox\Gen;

function gen($from, $to) {
  for ($i = $from; $i <= $to; $i++)
    yield $i;
}


$arr = iterator_to_array(Gen::zero());
var_dump($arr === []);

$arr = iterator_to_array(Gen::single(1));
var_dump($arr === [1]);

$arr = iterator_to_array(Gen::take(Gen::continually(1), 100));
var_dump($arr === array_fill(0, 100, 1));

$x = 1;
$f = function () use (&$x) { return $x++; };
$arr = iterator_to_array(Gen::take(Gen::continuallyApply($f), 100));
var_dump($arr === range(1,100));

$arr = iterator_to_array(Gen::fill(100, 47));
var_dump($arr === array_fill(0, 100, 47));

$x = 1;
$f = function () use (&$x) { return $x++; };
$arr = iterator_to_array(Gen::fillApply(100, $f));
var_dump($arr === range(1,100));

$arr = iterator_to_array(Gen::take(Gen::from(10, 5), 5));
var_dump($arr === [10,15,20,25,30]);

$f = function ($x) { return $x * 2; };
$arr = iterator_to_array(Gen::take(Gen::iterate(1, $f), 8));
var_dump($arr === [1,2,4,8,16,32,64,128]);

$arr = iterator_to_array(Gen::range(10, 47, 8));
var_dump($arr === [10,18,26,34,42]);

$f = function ($i) { return pow(2, $i); };
$arr = iterator_to_array(Gen::tabulate(8, $f));
var_dump($arr === [1,2,4,8,16,32,64,128]);

// ***

$arr = iterator_to_array(Gen::sliding(gen(1, 6), 3));
var_dump($arr === [[1,2,3], [2,3,4], [3,4,5], [4,5,6]]);

$arr = iterator_to_array(Gen::sliding(gen(1, 6), 10));
var_dump($arr === [[1,2,3,4,5,6]]);

$arr = iterator_to_array(Gen::sliding(gen(1, 6), 3, 2));
var_dump($arr === [[1,2,3], [3,4,5], [5,6]]);

$arr = iterator_to_array(Gen::grouped(gen(1, 6), 3));
var_dump($arr === [[1,2,3], [4,5,6]]);

$arr = iterator_to_array(Gen::grouped(gen(1, 6), 4));
var_dump($arr === [[1,2,3,4], [5,6]]);

$f = function ($x) { return $x + 1; };
$arr = iterator_to_array(Gen::map(Gen::map(gen(1,5), $f), $f));
var_dump($arr === [3,4,5,6,7]);
