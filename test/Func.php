<?php

error_reporting(E_ALL);

require_once __DIR__.'/../src/FuncLike.php';
require_once __DIR__.'/../src/BooleanCombinators.php';
require_once __DIR__.'/../src/Func.php';

use Atrox\Func;


$cf = Func::make('substr')->curried();

$a = $cf('0123456789');

var_dump($a(0, 2) === '01');
var_dump($a(2, 2) === '23');

$cf = Func::curry('substr', 3);
$f = $cf('0123456789', 1);
var_dump($f(1) === '1');
var_dump($f(4) === '1234');

$f = Func::make('substr')->curried(999);
$f = $f('0123456789', 2, 2);
var_dump($f() === '23');

$f = Func::make('implode')->curried(2);
$f = $f('+');           // function ($x) { return implode('+', $x); }
$f = $f->untupled();    // function () { return implode('+', func_get_args()); }
var_dump($f(1,2,3,4,5) === '1+2+3+4+5');

$f = Func::make('implode')->curried(2, '+')->untupled(1,2,3,4,5);


$f = function($a) {
  return function($b) use($a) {
    return function($c) use($a, $b) { return "$a $b $c"; };
  };
};
$cf = Func::make($f)->uncurried();
var_dump($cf(1,2) instanceof \Closure);
var_dump($cf(1) instanceof \Closure);
var_dump($cf(1,2,3) === '1 2 3');

$f = Func::make('explode')->tupled();
var_dump($f(array(',', 'x,y,z')) === explode(',', 'x,y,z'));
$fu = $f->untupled();
var_dump($fu(',', 'x,y,z') === explode(',', 'x,y,z'));

$f = Func::make(function ($arr) { return count($arr); })->untupled();
var_dump($f(1,2,3,4,5,6,7,8,9) === 9);

$f = function ($i) { return $i * 10; };
$g = function ($i) { return $i + 10; };

var_dump(Func::make($f)->compose($g)->invoke(10) === 200);
var_dump(Func::make($f)->andThen($g)->invoke(10) === 110);


// boolean composition
$f = Func::make(function($i) { return $i < 100; })->and(function ($i) { return $i > 0; });
var_dump($f(10)  === true);
var_dump($f(80)  === true);
var_dump($f(0)   === false);
var_dump($f(200) === false);


$f = Func::make('is_numeric')->not()->and('is_string'); // non numeric strings  function ($a) { return !is_numeric($a) && is_string($a) }

var_dump($f(1) === false);
var_dump($f('1') === false);
var_dump($f('asd') === true);
var_dump($f('12asd') === true);


$f = Func::make('is_array');
var_dump($f(array()) === true);
$fn = $f->not();
var_dump($fn(array()) === false);

$fs = array(function ($a) { return $a.'1'; }, function ($a) { return str_repeat($a, 10); }, function ($a) { return $a.'2'; });
$f = Func::chain($fs);
var_dump($f('_') === "_1_1_1_1_1_1_1_1_1_12");


echo "Func::arr\n";

$f = Func::arr("abcdef");
var_dump($f(0) === "a");
var_dump($f(1) === "b");

$f = Func::arr([1 => "one", 2 => "two", 3 => "three"]);
var_dump($f(1) === "one");
var_dump($f(2) === "two");

$f = Func::arr(new ArrayObject([1 => "one", 2 => "two", 3 => "three"]));
var_dump($f(1) === "one");
var_dump($f(2) === "two");

$f = Func::keySet("abcdef");
var_dump($f(0) === true);
var_dump($f(5) === true);
var_dump($f(6) === false);

$f = Func::keySet([1 => "one", 2 => "two", 3 => "three"]);
var_dump($f(1) === true);
var_dump($f(2) === true);
var_dump($f(0) === false);


