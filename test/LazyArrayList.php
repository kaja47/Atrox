<?php

require_once __DIR__.'/../LazyArrayList.php';

use Atrox\LazyArrayList;

function gen($from, $to) {
  for ($i = $from; $i <= $to; $i++)
    yield $i;
}

$a = new LazyArrayList(function() { return gen(1, 20); });

var_dump($a[0] === 1);
var_dump($a[1] === 2);
var_dump($a[5] === 6);

var_dump($a[9] === 10);
var_dump($a[7] === 8);
var_dump($a[1] === 2);
var_dump($a->count() === 20);
var_dump($a->take(200)->count() === 20);
var_dump($a->take(200)->take(30)->count() === 20);


$a = new LazyArrayList(function() { return gen(1, 20); });
var_dump(isset($a[0]) === true);;
var_dump(isset($a[20]) === false);

$a = new LazyArrayList(function() { return gen(1, 20); });
$b = $a->filter(function($i) { return $i % 2 === 0; });
var_dump($b->count() === 10);
