<?php

namespace Atrox;

// advanced bit-twiddling

abstract class AbstractIntArray {
  /** number of elements */
  protected $length;
  /** size of one element */
  protected $typeSize;
  /** string where data will be encoded */
  protected $data;

  function __construct($length, $data = null) {
    $this->length = $length;
    // todo check argument $data size
    $this->data = $data ?: str_repeat("\0", $length * $this->typeSize);
  }

  protected function checkIndex($idx) {
    if (!is_int($idx))
      throw new \InvalidArgumentException("index must be integer value");

    if ($idx < 0 || $idx >= $this->length)
      throw new \OutOfRangeException("index out of range");
  }

  protected function checkValue($val) {
    if (!is_int($val))
      throw new \InvalidArgumentException("Value must be integer.");
  }

  function getData() {
    return $this->data;
  }
}


/** store unsigned bytes */
final class ByteArray extends AbstractIntArray {
  protected $typeSize = 1;

  function get($idx) {
    $this->checkIndex($idx);

    return ord($this->data[$idx]);
  }

  function put($idx, $val) {
    $this->checkIndex($idx);
    $this->checkValue($val);

    if ($val < 0 || $val > 255)
      throw new \OutOfRangeException();

    $this->data[$idx] = chr($val);
  }
}


final class Int32Array extends AbstractIntArray {
  protected $typeSize = 4;

  function get($idx) {
    $this->checkIndex($idx);

    $bytes = substr($this->data, $idx * 4, 4);
    $res = unpack('V', $bytes);
    return $res[1];
  }

  function put($idx, $val) {
    $this->checkIndex($idx);
    $this->checkValue($val);

    $bytes = pack('V', $val);
    $base = $idx * 4;
    for ($i = 0; $i < 4; $i++) {
      $this->data[$base + $i] = $bytes[$i];
    }
  }
}


final class Int64Array extends AbstractIntArray {
  protected $typeSize = 8;

  function get($idx) {
    $this->checkIndex($idx);

    $bytes = substr($this->data, $idx * 8, 8);
    list(, $a, $b) = unpack('V2', $bytes);
    return ($a << 32) + $b;
  }

  function put($idx, $val) {
    $this->checkIndex($idx);
    $this->checkValue($val);

    $a = $val >> 32;
    $b = $val & 0xffffffff;
    $bytes = pack('VV', $a, $b);
    $base = $idx * 8;
    for ($i = 0; $i < 8; $i++) {
      $this->data[$base + $i] = $bytes[$i];
    }
  }
}


// ---


/** non-resizable bit set */
final class BitSet extends AbstractIntArray {
  protected $length;
  private $byteArray;

  function __construct($length, ByteArray $byteArray = null) {
    $this->length = $length;
    $this->byteArray = $byteArray ?: new ByteArray(ceil($length / 8));
  }

  function get($idx) {
    $this->checkIndex($idx);

    $byteIdx = (int) ($idx / 8);
    $bitIdx  = $idx % 8;
    $byte = $this->byteArray->get($byteIdx);
    return (($byte >> $bitIdx) & 1) === 1;
  }

  function put($idx, $val) {
    $this->checkIndex($idx);

    if (!is_bool($val))
      throw new \OutOfRangeException();

    $byteIdx = (int) ($idx / 8);
    $bitIdx  = $idx % 8;

    $byte = $this->byteArray->get($byteIdx);
    if ($val) {
      $byte |= (1 << $bitIdx);
    } else {
      $byte &= (0 << $bitIdx);
    }

    $this->byteArray->put($byteIdx, $byte);
  }

  function _or(BitSet $bs) {
    $a = $this->byteArray->getData();
    $b = $bs->byteArray->getData();
    $len = max($this->length, $bs->length);
    return new BitSet($len, new ByteArray(ceil($len / 8), $a | $b));
  }

  function _and(BitSet $bs) {
    $a = $this->byteArray->getData();
    $b = $bs->byteArray->getData();
    $len = min($this->length, $bs->length);
    return new BitSet($len, new ByteArray(ceil($len / 8), $a & $b));
  }

  function _xor(BitSet $bs) {
    $a = $this->byteArray->getData();
    $b = $bs->byteArray->getData();
    $len = min($this->length, $bs->length);
    return new BitSet($len, new ByteArray(ceil($len / 8), $a ^ $b));
  }

  function getData() {
    return $this->byteArray->getData();
  }
}


class BloomFilter {
  public $length;
  public $bitset;

  function hash($key) {
    return array(
      abs(hexdec(hash('crc32', 'm'.$key.'a')) % $this->length),
      abs(hexdec(hash('crc32', 'p'.$key.'b')) % $this->length),
      abs(hexdec(hash('crc32', 't'.$key.'c')) % $this->length)
    );
  }

  function __construct($length) {
    $this->length = $length;
    $this->bitset = new BitSet($length);
  }


  function add($key) {
    foreach ($this->hash($key) as $h)
      $this->bitset->put($h, true);
  }


  function contains($key) {
    foreach ($this->hash($key) as $h)
      if (!$this->bitset->get($h))
        return false;

    return true;
  }

  function merge(BloomFilter $bf) {
    if ($this->length !== $bf->length)
      throw new \Exception('cannot merge');

    if (count($this->hashes) !== count($this->hashes))
      throw new \Exception('cannot merge');

    foreach ($this->hashes as $i => $_) {
      if ($this->hashes[$i] !== $bf->hashes[$i])
        throw new \Exception('cannot merge');
    }

    $bf = new BloomFilter($this->length);
    $bf->bitset = $this->bitset->_or($bf->bitset);
    return $bf;
  }


  /**
   * Reports the false positive rate of the current bloom filter
   * @param  int $numItems number of items inserted in the bloom filter
   */
  function falsePositiveRate($numItems) {
    $k = count($this->hash('1'));
    return pow(1 - pow(1 - 1/$this->length, $k * $numItems), $k);
  }

  function getData() {
    return $this->bitset->getData();
  }

}
