<?php

require_once __DIR__.'/../Gen.php';

use Atrox\Gen;

function gen($from, $to) {
  for ($i = $from; $i <= $to; $i++)
    yield $i;
}



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
