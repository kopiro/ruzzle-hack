<?php

if (empty($argv[1])) die("Usage: php gendict.php {LANG}");

include 'class.ruzzle.php';

$r = new RuzzleHack($argv[1]);
$r->prepare_dict();
$r->prepare_nearest_map();