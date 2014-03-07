<?php

require_once __DIR__.'/../src/Monoid.php';

use Atrox\Monoid as M;

var_dump(M::num()->plus(1, 2) === 3);
var_dump(M::num()->sum([1, 2, 3, 4]) === 10);
var_dump(M::tuple([M::num(), M::max()])->sum([[1, 1], [1, 10], [1, 100]]) === [3, 100]);
var_dump(M::map(M::num())->sum([['a' => 1], ['a' => 2, 'b' => 3], ['b' => 4]]) === ['a' => 3, 'b' => 7]);
