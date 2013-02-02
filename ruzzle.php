<?php

include 'class.ruzzle.php';

$r = new RuzzleHack();

$r->init();
$r->load_set($argv[1]);

echo "YOUR MATRIX:\n";
$r->print_matrix();

echo "YOUR WORDS:\n";
$r->solve();
