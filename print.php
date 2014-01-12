<?php

if (empty($argv[1])) die("Usage: php gendict.php {LANG} {LINEARMATRIX}");

include 'class.ruzzle.php';
$r = new RuzzleHack($argv[1]);

$r->init();
$r->load_set($argv[2]);

echo "YOUR MATRIX:\n";
$r->print_matrix();

echo PHP_EOL;
echo "Calculating..\n";
$r->calculate_words();

echo "Sorting..\n";
$r->order_words();

$r->prettyprint_words();
