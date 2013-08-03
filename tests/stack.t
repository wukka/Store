#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;

$limit = 10;

T::plan(2);

$cache = new Store\EmbeddedTTL;
$app = 'test/cache/stack/' . microtime(TRUE) .'/';
$cl = new Store\Stack( $cache );
$values = array();
for ($i=0; $i<=$limit;$i++) {
        $value = "value_$i";
        $pos = $cl->add($value);
        $values[$pos] = $value;
}

unset($cl);

$cl = new Store\Stack( $cache );
krsort( $values );
T::is($cl->recent(400), $values, 'all the items added to the list show up, sorted by most recently added');
T::is( $cl->count(), 11, 'got expected count of the items in the list');

