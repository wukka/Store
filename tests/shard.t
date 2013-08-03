#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;


$a = new Store\EmbeddedTTL;
$b = new Store\EmbeddedTTL;

$closure = function( $key ) use ( $a, $b ){
    $hash = abs( crc32( $key) ) % 2;
    return $hash ? $a : $b;
};


$cache = new Store\Shard( $closure );

include __DIR__ . '/generic_tests.php';
