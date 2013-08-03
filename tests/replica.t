#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$plan_ct = 36;

$cache = new Store\Replica(array(new Store\EmbeddedTTL(new \Wukka\Store), new Store\EmbeddedTTL(new \Wukka\Store)));
include __DIR__ . '/generic_tests.php';


$a = new Store\EmbeddedTTL;
$b = new Store\EmbeddedTTL;

$m = new Store\Replica(array($a, $b));
T::ok( $m instanceof Store\Iface, 'Store\replica instantiated successfully');

$key = 'test' . microtime(TRUE);
$res = $m->get($key);
T::ok( $res === NULL, 'send get request for a key, got no data back');
$res = $m->set( $key, 1, 30);
T::ok( $res, 'write data into the key, got an ok response back');
$res = $m->get($key);
T::is( $res, 1, 'read the data again, got my value back');
T::is( $a->get($key), 1, 'correct value found in the first replica');
T::is( $b->get($key), 1, 'correct value found in the second replica');
T::is( $m->get( array( $key, 'non-existent-' . $key) ), array( $key=>1), 'multi-get returns correct data');

$key = 'test__' . microtime(TRUE);

T::ok( ! $m->replace( $key, 1, 30 ), 'replacing non-existent key fails');

$res = $m->add( $key, 1, 30);
T::is( $res, TRUE, 'adding a new key');

$res = $m->add( $key, 1, 30);
T::is( $res, FALSE, 'trying to add the key again fails');
$res = $m->get($key);

T::is( $res, 1, 'key has the value we added earlier');
T::is( $a->get($key), 1, 'correct value found in the first replica');
T::is( $b->get($key), 1, 'correct value found in the second replica');


$res = $m->replace( $key, 2, 0, 30 );
T::is( $res, TRUE, 'replacing the key works');
$res = $m->get($key);

T::is( $res, 2, 'key now has new value of replacement');
T::is( $a->get($key), 2, 'correct value found in the first replica');
T::is( $b->get($key), 2, 'correct value found in the second replica');
$res = $m->increment($key);
T::is( $res, 3, 'incrementing the key');
$res = $m->get( $key );
T::is( $res, 3, 'key now matches the value we incremented to');
T::is( $a->get($key), 3, 'correct value found in the first replica');
T::is( $b->get($key), 3, 'correct value found in the second replica');

$res = $m->increment($key, 7);
T::is( $res, 10, 'incrementing the key by 7');
$res = $m->get($key);
T::is( $res, 10, 'reading the data back returns 10');
T::is( $a->get($key), 10, 'correct value found in the first replica');
T::is( $b->get($key), 10, 'correct value found in the second replica');

$res = $m->decrement($key, 7);
T::is( $res, 3, 'decrementing the key by 7');
$res = $m->get($key);
T::is( $res, 3, 'reading the data back returns 3');
T::is( $a->get($key), 3, 'correct value found in the first replica');
T::is( $b->get($key), 3, 'correct value found in the second replica');

$res = $m->decrement($key);
T::is( $res, 2, 'decrementing the key');
$res = $m->get( $key );
T::is( $res, 2, 'key now matches the value we decremented to');
T::is( $a->get($key), 2, 'correct value found in the first replica');
T::is( $b->get($key), 2, 'correct value found in the second replica');

T::ok($m->delete( $key ), 'successfully deleted the key');
T::is( $a->get($key), FALSE, 'correct value found in the first replica');
T::is( $b->get($key), FALSE, 'correct value found in the second replica');

