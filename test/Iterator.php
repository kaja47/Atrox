<?php

error_reporting(E_ALL);

require __DIR__.'/../src/Iterator.php';


use Atrox\Iterator;
use Atrox\Gen;

function gen($from, $to) {
  for ($i = $from; $i <= $to; $i++)
   yield $i;
}


$lessThanFour = function ($a) { return $a < 4; };
$even         = function ($a) { return $a % 2 == 0; };
$sumFunction  = function ($sum, $el) { return $sum + $el; };
$divFunction  = function ($sum, $el) { return $sum / $el; };
$plusTen      = function ($a) { return $a + 10; };
$timesTwo     = function ($a) { return $a * 2; };

$it = Iterator::of(gen(1,4), gen('a','d'));
var_dump(iterator_to_array($it) === [1,2,3,4,'a','b','c','d']);

$it = Iterator::of(range(1,4), range('a','d'));
var_dump(iterator_to_array($it) === [1,2,3,4,'a','b','c','d']);

$it = Iterator::of(gen(1,4))->append(gen('a','d'));
var_dump(iterator_to_array($it) === [1,2,3,4,'a','b','c','d']);

$it = Iterator::of(gen(1,2), gen(3,4), gen(5,6), gen(7,8));
var_dump(iterator_to_array($it) === [1,2,3,4,5,6,7,8]);

$it = Iterator::zero()->append(gen(1,2))->append(gen(3,4))->append(gen(5,6))->append(gen(7,8));
var_dump(iterator_to_array($it) === [1,2,3,4,5,6,7,8]);

$it = Iterator::of()->append(gen(1,2), gen(3,4), gen(5,6))->append(gen(7,8));
var_dump(iterator_to_array($it) === [1,2,3,4,5,6,7,8]);

// ***

$it = Iterator::of(gen(1,9))->map($timesTwo);
var_dump(iterator_to_array($it) === [2,4,6,8,10,12,14,16,18]);

$it = Iterator::of(gen(101,105))->mapPairs(function ($k, $v) { return $k; });
var_dump(iterator_to_array($it) === [0,1,2,3,4]);

$it = (new Iterator(Gen::of([5=>10,10=>20,15=>30])))->mapPairs(function ($k, $v) { return $k+$v; });
var_dump(iterator_to_array($it) === [5=>15,10=>30,15=>45]);

$it = Iterator::of(gen(1,100))->filter($lessThanFour);
var_dump(iterator_to_array($it) === [1,2,3]);

$it = Iterator::of([1,2,3,4,3,2,1])->filter($lessThanFour);
var_dump(iterator_to_array($it) === [0=>1,1=>2,2=>3,4=>3,5=>2,6=>1]);

$it = Iterator::of(gen(1, PHP_INT_MAX))->map($plusTen)->filter($even)->take(3);
var_dump(iterator_to_array($it) === [1=>12, 3=>14, 5=>16]);

$it = Iterator::of(gen(1,PHP_INT_MAX))->filter(function($a) { return $a % 3 !== 0; })->map(function($a) { return $a * 10; })->slice(2, 2)->append(Iterator::of([999, 1000]));
var_dump(iterator_to_array($it) === [40, 50, 999, 1000]);

$it = Iterator::of(gen(1, PHP_INT_MAX))->slice(1,3);
var_dump(iterator_to_array($it) === [1=>2, 2=>3, 3=>4]);

$it = Iterator::of(gen(1,PHP_INT_MAX))->take(5);
var_dump(iterator_to_array($it) === [1,2,3,4,5]);

$it = Iterator::of(gen(1,PHP_INT_MAX))->takeWhile($lessThanFour);
var_dump(iterator_to_array($it) === [1,2,3]);

$it = Iterator::of([1,2,3,4,3,2,1])->takeWhile($lessThanFour);
var_dump(iterator_to_array($it) === [1,2,3]);

$it = Iterator::of([1,2,3,4])->flatMap(function ($x) { return gen(1, $x); });
var_dump(iterator_to_array($it) === [1,1,2,1,2,3,1,2,3,4]);

$it = Iterator::of([1,2,3,4])->flatMap(function ($x) { return range(1, $x); });
var_dump(iterator_to_array($it) === [1,1,2,1,2,3,1,2,3,4]);

$it = Iterator::of([1,2,3,4])->padTo(6,0);
var_dump(iterator_to_array($it) === [1,2,3,4,0,0]);

$it = Iterator::of([1,2,3,4])->zip([1,2,3]);
var_dump(iterator_to_array($it) === [[1,1], [2,2], [3,3]]);

$it = Iterator::of([1,2,3])->zip([1,2,3,4]);
var_dump(iterator_to_array($it) === [[1,1], [2,2], [3,3]]);

$it = Iterator::of(gen(1,10))->grouped(3);
var_dump(iterator_to_array($it) === [[1,2,3],[4,5,6],[7,8,9],[10]]);

$it = Iterator::of(gen(1,9))->grouped(3);
var_dump(iterator_to_array($it) === [[1,2,3],[4,5,6],[7,8,9]]);

$it = Iterator::of(gen(1,6))->sliding(3);
var_dump(iterator_to_array($it) === [[1,2,3],[2,3,4],[3,4,5],[4,5,6]]);

$it = Iterator::of(gen(1,6))->sliding(3, 2);
var_dump(iterator_to_array($it) === [[1,2,3],[3,4,5],[5,6]]);

$it = Iterator::of(gen(1,5))->sliding(3, 2);
var_dump(iterator_to_array($it) === [[1,2,3],[3,4,5]]);


$sum = Iterator::of(gen(1,10))->foldLeft(0, $sumFunction);
var_dump($sum === 55);

$div = Iterator::of([2,5,10])->foldLeft(3000, $divFunction);
var_dump($div === 30);
