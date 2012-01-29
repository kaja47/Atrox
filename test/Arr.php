<?php

error_reporting(E_ALL);

require __DIR__.'/../Arr.php';

use Atrox\Arr;

$sourceArray = array(1,2,3,4,5,6,7,8,9);
$arr = Arr::make(1,2,3,4,5,6,7,8,9);
$arr2 = Arr::from($sourceArray);
$arr3 = Arr::range(1,9);

$shortarr = Arr::make(0,1,2);

$strarr = Arr::make('1', '22', '333', '4444', '55555', 'xxxxxx');
$multiarr = Arr::make(array(1,2,3), array(10,20,30), array(100,200,300));

var_dump($arr->toArray()     === $sourceArray);
var_dump($arr2->toArray()    === $sourceArray);
var_dump($arr3->toArray()    === $arr->toArray());
var_dump($arr->mkString()    === '123456789');
var_dump($arr->mkString(',') === '1,2,3,4,5,6,7,8,9');
var_dump($arr->mkString('(', ',', ')') === '(1,2,3,4,5,6,7,8,9)');

var_dump($arr->contains(6)   === TRUE);
var_dump($arr->contains(10)  === false);
var_dump($arr->contains('6') === false);
var_dump($arr->find(function ($v) { return $v > 4; }) === 5);
var_dump($arr->find(function ($v) { return $v > 20; }) === null);
var_dump($arr->count(function ($v) { return $v % 2 == 0; }) === 4);
var_dump($arr->count('is_string') === 0);
var_dump($arr->count() === 9);
var_dump($arr->size() === 9);
var_dump($arr->max() === 9);
var_dump($arr->min() === 1);
var_dump($arr->product() === 362880);
var_dump($arr->sum() === 45);
var_dump($arr->isEmpty() === false);
var_dump($arr->nonEmpty() === true);
var_dump($arr->indexOf(4) === 3);
var_dump($arr->indexOf(10) === false);
var_dump(Arr::make(1,2,3,1,2,3)->indexOf(1, 3) === 3);
var_dump(Arr::make(1,2,3,1,2,3)->indexOf(1, 4) === false);
var_dump(Arr::make(1,2,3,1,2,3)->lastIndexOf(1) === 3);
var_dump(Arr::make(1,2,3,1,2,3)->lastIndexOf(1, 2) === 0);
var_dump(Arr::make(1,2,3,1,2,3)->lastIndexOf(1, -3) === 0);
var_dump($arr->indexWhere(function ($v) { return $v % 3 === 0; }) === 2);
var_dump($arr->indexWhere(function ($v) { return $v % 3 === 0; }, 3) === 5);
var_dump($arr->indexWhere(function ($v) { return $v % 3 === 0; }, 9) === -1);
var_dump($arr->indexWhere('is_string') === -1);
var_dump($arr->indexOfSlice(array(1,2,3)) === 0);
var_dump($arr->indexOfSlice(array(1,2,4)) === -1);
var_dump($arr->indexOfSlice(array(4,5,6)) === 3);
var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->indexOfSlice(array(1,2,3), 0) === 0);
var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->indexOfSlice(array(1,2,3), 1) === 3);
//var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->lastIndexOfSlice(array(1,2,3)) === 6);
//var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->lastIndexOfSlice(array(1,2,3), 8) === 6);
//var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->lastIndexOfSlice(array(1,2,3), 6) === 6);
//var_dump(Arr::make(1,2,3,1,2,3,1,2,3)->lastIndexOfSlice(array(1,2,3), 5) === 3);

var_dump($arr->forall(function ($v) { return $v < 10; }) === true);
var_dump($arr->forall(function ($v) { return $v < 5; }) === false);
var_dump($arr->prefixLength(function ($v) { return $v < 4; }) === 3);
var_dump($arr->segmentLength(function ($v) { return $v < 4; }, 1) === 2);
var_dump($arr->corresponds(array(10,20,30,40,50,60,70,80,90), function($a, $b) { return $a * 10 === $b; }) === true);
var_dump($arr->corresponds(Arr::make(10,20,30,40,50,60,70,80,90), function($a, $b) { return $a * 10 === $b; }) === true);
var_dump($arr->corresponds(array(10,20,30,40,50,50,50,50,50), function($a, $b) { return $a * 10 === $b; }) === false);
var_dump($arr->corresponds(array(10,20,30,40,50,60,70,80,90,100), function($a, $b) { return $a * 10 === $b; }) === false);
var_dump($arr->corresponds(array(10,20,30,40,50,60,70,80,90,null), function($a, $b) { return $a === $b; }) === false);
var_dump($arr->corresponds(array(10,20,30,40,50,60,70,80), function($a, $b) { return $a * 10 === $b; }) === false);
var_dump($arr->sameElements(array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9)));
var_dump($arr->sameElements(array(30=>1,29=>2,28=>3,27=>4,26=>5,25=>6,24=>7,23=>8,22=>9)));

var_dump(Arr::make(1,2,3,4)->startsWith(Arr::make(1,2,3)) === true);
var_dump(Arr::make(1,2,3,4)->startsWith(Arr::make(1,2,3,4)) === true);
var_dump(Arr::make(1,2,3,4)->startsWith(Arr::make(1,2,3,4,5)) === false);
var_dump(Arr::make(1,2,3,4)->startsWith(Arr::make(2,3)) === false);
var_dump(Arr::make(1,2,3,4)->startsWith(Arr::make(2,3,4,5)) === false);

var_dump(Arr::make(1,2,3,4)->endsWith(Arr::make(2,3,4)) === true);
var_dump(Arr::make(1,2,3,4)->endsWith(Arr::make(1,2,3,4)) === true);
var_dump(Arr::make(1,2,3,4)->endsWith(Arr::make(1,2,3,4,5)) === false);
var_dump(Arr::make(1,2,3,4)->endsWith(Arr::make(2,3)) === false);

var_dump($strarr->maxBy('strlen') === 'xxxxxx');
var_dump($strarr->minBy('strlen') === '1');

var_dump($arr->foldLeft(0, function ($sum, $a) { return $sum + $a; }) === 45);
var_dump($arr->foldLeft("0", function ($sum, $a) { return $sum . $a; }) === '0123456789');
var_dump($arr->foldRight("0", function ($a, $sum) { return $sum . $a; }) === '0987654321');
var_dump($arr->reduceLeft(function ($sum, $a) { return $sum + $a; }) === 45);
var_dump($arr->reduceLeft(function ($sum, $b) { return $sum . $b; }) === '123456789');
var_dump($arr->reduceRight(function ($b, $sum) { return $sum . $b; }) === '987654321');

var_dump($arr->head() === 1);
var_dump($arr->last() === 9);
var_dump($arr->tail()->toArray() === array(1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,8=>9));
var_dump($arr->init()->toArray() === array(0=>1,1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8));
var_dump($arr->take(3)->toArray() === array(1,2,3));
var_dump($arr->drop(3)->toArray() === array(3=>4,4=>5,5=>6,6=>7,7=>8,8=>9));
var_dump($arr->slice(3, 2)->toArray() === array(3=>4,4=>5));
var_dump($arr->drop(3)->take(2)->toArray() === array(3=>4,4=>5));
var_dump($arr->takeWhile(function ($v) { return $v <= 3; })->toArray() === array(1,2,3));
var_dump($arr->takeWhile(function ($v) { return $v > 3; })->toArray() === array());
var_dump($arr->takeRight(2)->toArray() === array(7=>8,8=>9));
var_dump($arr->dropRight(7)->toArray() === array(0=>1,1=>2));
var_dump($arr->dropWhile(function ($v) { return $v <= 7; })->toArray() === array(7=>8,8=>9));
var_dump(Arr::make(1,2,3,4,5)->tails()->toArrayRecursive() === array(array(1, 2, 3, 4, 5), array(1=>2, 2=>3, 3=>4, 4=>5), array(2=>3, 3=>4, 4=>5), array(3=>4, 4=>5), array(4=>5), array()));
var_dump(Arr::make(1,2,3,4,5)->inits()->toArrayRecursive() === array(array(1, 2, 3, 4, 5), array(1, 2, 3, 4), array(1, 2, 3), array(1, 2), array(1), array()));
var_dump($arr->sliding(1)->toArrayRecursive() === array(array(0=>1), array(1=>2), array(2=>3), array(3=>4), array(4=>5), array(5=>6), array(6=>7), array(7=>8), array(8=>9)));
var_dump($arr->sliding(7)->toArrayRecursive() === array(array(0=>1, 1=>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7), array(1=>2, 2=>3, 3=>4, 4=>5, 5=>6, 6=>7, 7=>8), array(2=>3, 3=>4, 4=>5, 5=>6, 6=>7, 7=>8, 8=>9)));
var_dump($arr->sliding(2, 2)->toArrayRecursive() === array(array(0=>1, 1=>2), array(2=>3, 3=>4), array(4=>5, 5=>6), array(6=>7, 7=>8), array(8=>9)));
var_dump($arr->sliding(4, 3)->toArrayRecursive() === array(array(0=>1, 1=>2, 2=>3, 3=>4), array(3=>4, 4=>5, 5=>6, 6=>7), array(6=>7, 7=>8, 8=>9)));

var_dump(Arr::make(1,2,3,1,2,8)->distinct()->toArray() === array(0=>1,1=>2,2=>3,5=>8));
var_dump(Arr::from(array(array(1,2,3), array(4,5,6), array(7,8,9)))->transpose()->toArray() === array(array(1, 4, 7), array(2, 5, 8), array(3, 6, 9)));
var_dump(Arr::from(array(array(1,2,3,4,5,6,7,8,9)))->transpose()->toArray() === array(array(1), array(2), array(3), array(4), array(5), array(6), array(7), array(8), array(9)));
var_dump(Arr::from(array(array(1,2,3,4,5,6,7,8,9), array(10,11,12,13,14,15,16,17,18)))->transpose()->toArray() === array(array(1, 10), array(2, 11), array(3, 12), array(4, 13), array(5, 14), array(6, 15), array(7, 16), array(8, 17), array(9, 18)));
var_dump(Arr::from(array(array(1,2), array(3,4), array(5,6), array(7,8)))->transpose()->toArray() === array(array(1, 3, 5, 7), array(2, 4, 6, 8)));

list($l, $r) = $arr->partition(function($v) { return $v % 2 === 0; });
var_dump($l->toArray() === array(1=>2,3=>4,5=>6,7=>8));
var_dump($r->toArray() === array(0=>1,2=>3,4=>5,6=>7,8=>9));

list($l, $r) = $arr->span(function($v) { return $v % 2 === 1; });
var_dump($l->toArray() === array(0=>1));
var_dump($r->toArray() === array(1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,8=>9));

var_dump($shortarr->reverse()->toArray() === array(2=>2, 1=>1, 0=>0));


var_dump($arr->scanLeft('', function ($sum, $a) { return $sum.' '.$a; })->toArray() === array("", " 1", " 1 2", " 1 2 3", " 1 2 3 4", " 1 2 3 4 5", " 1 2 3 4 5 6", " 1 2 3 4 5 6 7", " 1 2 3 4 5 6 7 8", " 1 2 3 4 5 6 7 8 9"));
var_dump($arr->scanRight('', function ($a, $sum) { return $sum.' '.$a; })->toArray() === array(" 9 8 7 6 5 4 3 2 1", " 9 8 7 6 5 4 3 2", " 9 8 7 6 5 4 3", " 9 8 7 6 5 4", " 9 8 7 6 5", " 9 8 7 6", " 9 8 7", " 9 8", " 9", ""));


var_dump($arr->map(function($a) { return $a.'x'; })->toArray() === array('1x','2x','3x','4x','5x','6x','7x','8x','9x'));
var_dump($arr->filter(function($a) { return $a % 3 === 0; })->toArray() === array(2=>3, 5=>6, 8=>9));

var_dump($arr->filterKeys(function($k) { return $k % 3 === 0; })->toArray() === array(0=>1, 3=>4, 6=>7));

var_dump($arr->padTo(12, 0)->toArray() === array(1,2,3,4,5,6,7,8,9,0,0,0));
var_dump($arr->padTo(1, 0)->toArray() === array(1,2,3,4,5,6,7,8,9));

var_dump($arr->groupBy(function ($v) { return $v % 3; })->toArrayRecursive() === array(1 => array(0=>1, 3=>4, 6=>7), 2 => array(1=>2, 4=>5, 7=>8), 0 => array(2=>3, 5=>6, 8=>9)));
var_dump($arr->grouped(3)->toArrayRecursive() === array(array(0=>1, 1=>2, 2=>3), array(3=>4, 4=>5, 5=>6), array(6=>7, 7=>8, 8=>9)));

var_dump($arr(0) === 1);
var_dump($arr(0) === $arr[0]);
var_dump(Arr::make(0,5)->map($strarr)->toArray() === array('1', 'xxxxxx'));

var_dump($multiarr->flatten()->toArray() === array(1,2,3,10,20,30,100,200,300));
var_dump(Arr::make(1,2,3)->flip()->toArray() === array(1=>0,2=>1,3=>2));

var_dump(Arr::make(10,20,30)->remap(function ($k, $v) { return array($v => $v); })->toArray() === array(10=>10,20=>20,30=>30));
var_dump(Arr::make(10,20,30)->remap(function ($k, $v) { return array($v => $k); })->toArray() === array(10=>0,20=>1,30=>2));
var_dump(Arr::make(10,20,30)->remap(function ($k, $v) { return array($v => $v, $v+1 => $v+1); })->toArray() === array(10=>10,11=>11,20=>20,21=>21,30=>30,31=>31));

$defarr = $arr->withDefaultValue(100);
var_dump($defarr[0] === 1);
var_dump($defarr[1] === 2);
var_dump($defarr[20] === 100);
var_dump($defarr->getOrElse(20, 999) === 999);

$deffunarr = $arr->withDefault(function($k) { return $k * 10; }); 
var_dump($deffunarr[0] === 1);
var_dump($deffunarr[8] === 9);
var_dump($deffunarr[10] === 100);

var_dump($arr->getOrElse(1, 999) === 2);
var_dump($arr->getOrElse(20, 999) === 999);

var_dump(Arr::make(1,2,3)->append(4,5,6)->toArray() === array(1,2,3,4,5,6));
var_dump(Arr::make(1,2,3)->prepend(4,5,6)->toArray() === array(4,5,6,1,2,3));
var_dump($arr->updated(0,10)->toArray() === array(10,2,3,4,5,6,7,8,9));
var_dump($arr->updated(array(1=>'one',4=>'four'))->toArray() === array(1,'one',3,4,'four',6,7,8,9));
var_dump($arr->updated(array(10=>'end'))->toArray() === array(1,2,3,4,5,6,7,8,9,10=>'end'));
var_dump($arr->toArray() === array(1,2,3,4,5,6,7,8,9));
var_dump(Arr::make(1,2,3,4,5)->patch(1, array(10,11,12,13), 0)->toArray() === array(1, 10, 11, 12, 13, 2, 3, 4, 5));
var_dump(Arr::make(1,2,3,4,5)->patch(0, array(10,11,12,13), 10)->toArray() === array(10, 11, 12, 13));


$unsorted = array(5=>4, 6=>3, 7=>2, 8=>1, 4=>5, 3=>6);
$unsortedArr = Arr::from($unsorted);
var_dump($unsortedArr->sort()->toArray() === array(8=>1, 7=>2, 6=>3, 5=>4, 4=>5, 3=>6));
var_dump($unsortedArr->sortKeys()->toArray() === array(3=>6, 4=>5, 5=>4, 6=>3, 7=>2, 8=>1));
