<?php

include 'class.ruzzle.php';

$r = new RuzzleHack();

$r->init($argv[1]);
$r->load_set($argv[2]);

echo "YOUR MATRIX:\n";
$r->print_matrix();

echo "YOUR WORDS:\n";
$r->calculate_words();
$r->order_words();
$r->prettyprint_words();
