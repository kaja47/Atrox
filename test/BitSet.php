<?php

error_reporting(E_ALL);

require __DIR__.'/../src/IntArray.php';

use Atrox\BitSet;


$a = new BitSet(128);
$a->put(0, false);
$a->put(1, true);
$a->put(2, true);
$a->put(4, true);
$a->put(8, true);
$a->put(16, true);
$a->put(32, true);
$a->put(32, false);

$b = new BitSet(128);
$b->put(1, true);
$b->put(2, true);
$b->put(3, true);
$b->put(4, true);
$b->put(5, true);

var_dump($a->get(0) === false);
var_dump($a->get(1) === true);
var_dump($a->get(2) === true);
var_dump($a->get(3) === false);
var_dump($a->get(32) === false);

$aorb = $a->_or($b);

var_dump($aorb->get(0) === false);
var_dump($aorb->get(1) === true);
var_dump($aorb->get(2) === true);
var_dump($aorb->get(3) === true);
var_dump($aorb->get(4) === true);
var_dump($aorb->get(9) === false);

$aandb = $a->_and($b);

var_dump($aandb->get(0) === false);
var_dump($aandb->get(1) === true);
var_dump($aandb->get(2) === true);
var_dump($aandb->get(3) === false);
var_dump($aandb->get(4) === true);
var_dump($aandb->get(5) === false);
var_dump($aandb->get(9) === false);

$axorb = $a->_xor($b);

var_dump($axorb->get(0) === false);
var_dump($axorb->get(1) === false);
var_dump($axorb->get(2) === false);
var_dump($axorb->get(3) === true);
var_dump($axorb->get(4) === false);
var_dump($axorb->get(5) === true);
var_dump($axorb->get(8) === true);
var_dump($axorb->get(9) === false);
