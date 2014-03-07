<?php

namespace Atrox;


/**
 * Based on this gem: http://www.johndcook.com/standard_deviation.html
 */
class RunningStat {

  private $n = 0; // int
  private $oldM, $newM, $oldS, $newS; // double
  private $first = true; // mod
  private $min, $max, $sum;

  function clear() {
    $this->n = 0;
    $this->first = true; // mod
  }

  function push($val, $w = 1) {
    $this->n += $w; // mod

    // See Knuth TAOCP vol 2, 3rd edition, page 232
    if ($this->first) { // mod
      $this->oldM = $this->newM = $val;
      $this->oldS = 0.0;

      $this->max = $val;
      $this->min = $val;
      $this->sum = $val;

    } else {
      //$this->newM = $this->oldM + ($val - $this->oldM) / $this->n;
      $this->newM = ($this->oldM * ($this->n - $w) + $w*$val) / $this->n; // mod
      $this->newS = $this->oldS + ($val - $this->oldM) * ($val - $this->newM) *$w*$w; // mod

      // set up for next iteration
      $this->oldM = $this->newM;
      $this->oldS = $this->newS;

      $this->max = max($this->max, $val);
      $this->min = min($this->min, $val);
      $this->sum += $val;
    }

    $this->first = false;
  }

  function count() {
    return $this->n;
  }

  function average() {
    return ($this->n > 0) ? $this->newM : 0.0;
  }

  function variance() {
    return ($this->n > 1) ? $this->newS / ($this->n) : 0.0;
  }

  function stdDev() {
    return sqrt($this->variance());
  }

  function max() {
    return $this->max;
  }

  function min() {
    return $this->min;
  }

  function sum() {
    return $this->sum;
  }

}
