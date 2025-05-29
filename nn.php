<?php
define ('ESC', "\033");
define ('ANSI_RED', ESC."[31m");
define ('ANSI_GREEN', ESC."[32m");
define ('ANSI_CLOSE', ESC."[0m");

class NN {
  public Matrix $in, $out;
  public Array $hidden, $weights, $biases;
  public function  __construct(Matrix $_in, $_ArrHidden, Matrix $_out) {
    $this->in = $_in;
    $this->hidden = $_ArrHidden;
    $this->out = $_out;
    if (is_array($_ArrHidden)) {
      foreach ($_ArrHidden as $count) {
        $this->hidden[] = Matrix::random($count, 1);
      }
    }
  }

  public function setBiases ($arrBiases) {
    $this->weights = $arrBiases;
  } 
  public function setWeights ($arrWeights) {
    $this->weights = $arrWeights;
  } 
}


function _assert($stmt, $desc) {
  if (!$stmt) {
    echo $desc . ANSI_RED . 'failed' . ANSI_CLOSE . "\n";
    return;
  }
  echo $desc . ANSI_GREEN . 'passed' . ANSI_CLOSE . "\n";
}

function assert_matrix_eq($m1, $m2, $desc) {
  _assert(json_encode($m1->value) === json_encode($m2->value),"[assert_matrix_eq] " .  $desc); 
}

class Matrix {
  public $value;
  private $toStringFn = 'printValue';
  public function __construct ($w, $h) {
    $row = array_fill(0, $w, 0);
    $this->value = array_fill(0, $h, $row);
  }
  public static function random ($w, $h) {
    $m = new Matrix($w, $h);
    for ($i = 0; $i < $h; $i++) {
      for ($j = 0; $j < $w; $j++) {
         $m->value[$i][$j] = (rand(1, 999999) / 1000000);
      }
    }
    return $m;
  }
  public static function fromArray($arr) {
    $new = new Matrix(0, 0);
    $new->value = $arr;
    return $new;
  }

  public function __toString() {
    $method = $this->toStringFn;
    $res = $this->$method();
    $this->toStringFn = 'printValue';
    return $res;
  }

  private function printValue() {
    $v = $this->value;
    $res = '';
    for ($i = 0; $i < count($v); $i++) {
      $res .=  implode(' ', $v[$i]) . "\n";
    }
    return $res;
  }

  public function dot(Matrix $m) {
    $v = $this->value;
    $w = $m->value;
    $dest = new Matrix(count($w[0]), count($v) );
    $height = count($dest->value); // h
    $width = count($dest->value[0]); // w
    for ($i = 0; $i < $height; $i++) {
      for ($j = 0; $j < $width; $j++) {
        $sum = 0;
          for($k = 0; $k< count($w); $k++) {
            $sum += $v[$i][$k] * $w[$k][$j];
          }
          $dest->value[$i][$j] = $sum;
      }
    } 
    return $dest;
  }

  public function add(Matrix $m) {
    $v = $this->value;
    $w = $m->value;
    $dest = new Matrix(count($v[0]), count($v));
    for ($i = 0; $i < count($v); $i++) {
      for ($j = 0; $j < count($v[0]); $j++) {
        $dest->value[$i][$j] = $v[$i][$j] + $w[$i][$j];
      }
    }
    return $dest;
  }
}
$a = Matrix::fromArray(
[
  [2, 2],
  [0, 3],
  [0, 4]
]);
$b = Matrix::fromArray([
  [2, 1, 2],
  [3, 2, 4]
]);
$expected = Matrix::fromArray([
  [10, 6, 12],
  [9, 6, 12],
  [12, 8, 16]
]);
$res = $a->dot($b);
assert_matrix_eq($res, $expected, "Matrix multiplication: ");
$res = $b->dot($a);
assert_matrix_eq($res, Matrix::fromArray([
  [4, 15],
  [6, 28]
]) , "Matrix multiplication the other way: ");
$res = $a->add($a);
assert_matrix_eq(
Matrix::fromArray([[1, 2]])->dot(
    Matrix::fromArray(
      [
        [9, 4],
        [8, 2]
      ]
    )
),
Matrix::fromArray([
  [25, 8]
]),
"Matrix multiplciation (1x2 * 2x2): "
);
assert_matrix_eq($res, Matrix::fromArray([
  [4, 4],
  [0, 6],
  [0, 8]
]), "Matrix addition(1): ");
$res = $b->add($b);
assert_matrix_eq($res, Matrix::fromArray([
  [4, 2, 4],
  [6, 4, 8]
]), "Matrix addition(2): ");
$nn = new NN(
  Matrix::fromArray([[1, 2]]),
  [8, 9],
  new Matrix(1, 1)
);
$nn->setWeights(
  [
    Matrix::fromArray(
      [
        [2, 4],
        [1, 2]
      ]
    ),
    Matrix::fromArray([
      [6, 7]
    ])
  ]
);
$res =  Matrix::fromArray([[1, 2]])->dot(
    Matrix::fromArray(
      [
        [9, 4],
        [8, 2]
      ]
    )
);
echo memory_get_peak_usage();
