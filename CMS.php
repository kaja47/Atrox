<?php

namespace Atrox;

// based on: 
// - https://github.com/addthis/stream-lib/blob/master/src/main/java/com/clearspring/analytics/stream/frequency/CountMinSketch.java
// - https://github.com/twitter/algebird/blob/fcf005e230a72102f0dc25678fd2c33104f32d42/algebird-core/src/main/scala/com/twitter/algebird/CountMinSketch.scala

class CountMinSketch {

  const PRIME_MODULUS = 2147483647; // (1 << 31) - 1;

  public $depth;
  public $width;
  public $eps;
  public $confidence;

  public $table; // SplFixedArray
  public $hashA; // long[]
  public $size = 0;

  public $heavyHitters;

  static function fromDim($depth, $width, $seed, $heavyHittersPct) {
    $eps = 2.0 / $width;
    $confidence = 1 - 1 / pow(2, $depth);

    return new CountMinSketch($depth, $width, $eps, $confidence, $seed);
  }

  static function fromProp($epsOfTotalCount, $confidence, $seed, $heavyHittersPct) {
    // 2/w = eps ; w = 2/eps
    // 1/2^depth <= 1-confidence ; depth >= -log2 (1-confidence)
    $width = (int) ceil(2 / $epsOfTotalCount);
    $depth = (int) ceil(-log(1 - $confidence) / log(2));

    return new CountMinSketch($depth, $width, $epsOfTotalCount, $confidence, $seed, $heavyHittersPct);
  }

  function __construct($depth, $width, $eps, $confidence, $seed, $heavyHittersPct = 100) {
    $this->depth = $depth;
    $this->width = $width;
    $this->eps   = $eps;
    $this->confidence = $confidence;

    $this->initTablesWith($depth, $width, $seed);

    $this->heavyHitters = new HeavyHitters($heavyHittersPct);
  }

  private function initTablesWith($depth, $width, $seed) {
    $this->table = \SplFixedArray::fromArray(array_fill(0, $depth * $width, 0));
    $this->hashA = [];
    mt_srand($seed);
    // We're using a linear hash functions
    // of the form (a*x+b) mod p.
    // a,b are chosen independently for each hash function.
    // However we can set b = 0 as all it does is shift the results
    // without compromising their uniformity or independence with
    // the other hashes.
    for ($i = 0; $i < $depth; ++$i) {
      $this->hashA[$i] = mt_rand();
    }
  }

  function getRelativeError() {
    return $this->eps;
  }

  function getConfidence() {
    return $this->confidence;
  }

  private function hash($item, $i) { // long item
    $hash = $this->hashA[$i] * $item;
    // A super fast way of computing x mod 2^p-1
    // See http://www.cs.princeton.edu/courses/archive/fall09/cos521/Handouts/universalclasses.pdf
    // page 149, right after Proposition 7.
    $hash += $hash >> 32;
    $hash &= self::PRIME_MODULUS;
    return $hash % $this->width;
  }

  function addInt($item, $count = 1) {
    if ($count < 0) {
      throw new IllegalArgumentException("Negative increments not implemented");
    }
    $est = PHP_INT_MAX;
    for ($i = 0; $i < $this->depth; ++$i) {
      $hash = $this->hash($item, $i);
      $p = $i * $this->width + $hash;
      $c = $this->table[$p];
      $this->table[$p] = $c + $count;
      $est = ($est < ($c+$count)) ? $est : ($c + $count); // faster than calling min()
    }
    $this->size += $count;

    $this->heavyHitters->updateHeavyHitters($item, $count, $est, $this->size);
  }

  function getHashBuckets($item, $depth, $width) {
    $buckets = [];
    $hash1 = crc32($item);
    $hash2 = crc32($item . $hash1);
    for ($i = 0; $i < $depth; ++$i) {
      $buckets[$i] = abs(($hash1 + $i * $hash2) % $width);
    }
    return $buckets;
  }

  function addString($item, $count = 1) {
    if ($count < 0) {
      throw new IllegalArgumentException("Negative increments not implemented");
    }

    $buckets = $this->getHashBuckets($item, $this->depth, $this->width);

    $hash1 = crc32($item);
    $hash2 = crc32($item . $hash1);
    for ($i = 0; $i < $this->depth; ++$i) {
      $bucket = abs(($hash1 + $i * $hash2) % $this->width);
      $this->table[$i * $this->width + $buckets[$i]] += $count;
    }
    $this->size += $count;

    $this->heavyHitters->updateHeavyHitters($item, $count, $this->estimateCountString($item), $this->size);
  }

  function getSize() {
    return $this->size;
  }

  /**
   * The estimate is correct within 'epsilon' * (total item count),
   * with probability 'confidence'.
   */
  function estimateCountInt($item) {
    $res = PHP_INT_MAX;
    for ($i = 0; $i < $this->depth; ++$i) {
      $res = min($res, $this->table[$i * $this->width + $this->hash($item, $i)]);
    }
    return $res;
  }

  function estimateCountString($item) { // string
    $res = PHP_INT_MAX;
    $buckets = $this->getHashBuckets($item, $this->depth, $this->width);
    for ($i = 0; $i < $this->depth; ++$i) {
      $res = min($res, $this->table[$i * $this->width + $buckets[$i]]);
    }
    return $res;
  }
}

final class HeavyHitters {
  public $hhs = []; // [item => count]
  public $heavyHittersPct; 

  function __construct($heavyHittersPct) {
    $this->heavyHittersPct = $heavyHittersPct;
  }

  function getItems() {
    return array_keys($this->hhs);
  }

  function updateHeavyHitters($item, $count, $estimate, $totalSize) {
    $oldItemCount = $estimate;
    $newItemCount = $oldItemCount + $count;
    $newTotalCount = $totalSize + $count;

    // If the new item is a heavy hitter, add it, and remove any previous instances.
    if ($newItemCount >= $this->heavyHittersPct * $newTotalCount) {
      $this->hhs[$item] = $newItemCount;
    }

    // Remove any items below the new heavy hitter threshold.
    $this->dropCountsBelow($this->heavyHittersPct * $newTotalCount);
  }

  function dropCountsBelow($minCount) {
    foreach ($this->hhs as $item => $count) {
      if ($count < $minCount)
        unset($this->hhs[$item]);
    }
  }
}


