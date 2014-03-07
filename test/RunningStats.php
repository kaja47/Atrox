<?php

require_once __DIR__.'/../src/RunningStats.php';

use Atrox\RunningStat;


for ($run = 0; $run < 100; $run++) {

  $arr = [];
  for ($i = 0; $i < rand(1, 100); $i++) {
    $arr[$i] = rand(0, 100);
  }

  $rs = new RunningStat;
  foreach ($arr as $v) {
    $rs->push($v);
  }

  var_dump($rs->count() === count($arr));
  var_dump($rs->sum() === array_sum($arr));
  var_dump($rs->max() === max($arr));
  var_dump($rs->min() === min($arr));
  var_dump($rs->average() === floatval(array_sum($arr) / count($arr)));

  $avg = array_sum($arr) / count($arr);
  $sum = 0;
  foreach ($arr as $v) {
    $sum += pow($v - $avg, 2);
  }
  var_dump($rs->stdDev() === sqrt($sum / count($arr)));
  echo "---\n";

}
