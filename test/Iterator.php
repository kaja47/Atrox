<?php

error_reporting(E_ALL);

require __DIR__.'/../Iterator.php';

use Atrox\Iterator;


$it1 = Iterator::fromArray([1,2,3,4,5,6,7,8,9]);
$it2 = Iterator::fromArray(['a', 'b', 'c', 'x']);

var_dump('append', iterator_to_array($it1->append($it2), false) === [1,2,3,4,5,6,7,8,9,'a','b','c','x']);

$lessThanFour = function ($a) { return $a < 4; };
var_dump('filter', iterator_to_array($it1->filter($lessThanFour)) === [1,2,3]);

$timesTwo = function ($a) { return $a * 2; };
var_dump('map',    iterator_to_array($it1->map($timesTwo)) === [2,4,6,8,10,12,14,16,18]);

var_dump('slice',  iterator_to_array($it1->slice(1,3)) === [1=>2, 2=>3, 3=>4]);

var_dump('sliceAppend', iterator_to_array($it1->slice(1,3)->append($it2->slice(1,2)), false) === [2, 3, 4, 'b', 'c']);

var_dump('everything', iterator_to_array(  $it1->filter(function($a) { return $a % 3 !== 0; })->map(function($a) { return $a * 10; })->slice(2, 2)->append(Iterator::fromArray([999, 1000]))  , false) === [40, 50, 999, 1000]);

var_dump('flatMap', iterator_to_array(  $it1->slice(0, 3)->flatMap(function ($a) { return Iterator::fromArray(range($a*10, $a*10+3)); })  , false) === [10,11,12,13,20,21,22,23,30,31,32,33]);
