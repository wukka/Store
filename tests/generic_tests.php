<?php

include __DIR__ . '/time_override.php';

use Wukka\Test as T;



if( ! isset( $skip_expiration_tests ) ) $skip_expiration_tests = FALSE;
if( ! isset( $plan_ct ) ) $plan_ct = 0;
T::plan(40 + $plan_ct);

$data = array();
for( $i = 1;    $i <= 3; $i++){
    $data[ 'wukka/cache/test/' . microtime(TRUE) . '/' . mt_rand(1, 10000) ] = $i;
}

$res = FALSE;

$ret = array();
foreach( $data as $k => $v ) {
    if( $ret[ $k ] = $cache->get( $k ) ) $res = TRUE;
}

T::ok( ! $res, 'none of the data exists before I write it in the cache');
if( $res ) T::debug( $ret );

$res = TRUE;
$ret = array();
foreach( $data as $k => $v ){
    if( ! $ret[ $k ] = $cache->set( $k, $v, 10) ) $res = FALSE;
}
T::ok( $res, 'wrote all of my data into the cache');
if( ! $res ) T::debug( $ret );

$res = TRUE;
foreach( $data as $k => $v ){
   if(  $cache->get( $k ) != $v ) $res = FALSE;
}
T::ok( $res, 'checked each key and got back what I wrote');

$ret = $cache->get( array_keys( $data ) );
$res = TRUE;
foreach( $data as $k => $v ){
    if( $ret[ $k ] != $v ) $res = FALSE;
}
T::ok( $res, 'grabbed the keys all at once, got what I wrote');

$k = 'wukka/cache/test/' . microtime(TRUE) . '/' . mt_rand(1, 10000);
T::ok( $cache->add( $k, 1, 10), 'adding a non-existent key');
T::ok( ! $cache->add( $k, 1, 10), 'second time, the add fails');

if( $skip_expiration_tests || ! method_exists( $cache, 'ttlEnabled') || ! $cache->ttlEnabled() ){
    T::ok(TRUE, 'skipping expire test');
} else {
    $_SERVER['TIME_OFFSET'] = 11;    
    T::ok( $cache->add( $k, 1, 10), 'after expiration time, add works');
}
T::ok( $cache->replace( $k, 1, 10 ), 'replace works after the successful add');

T::ok( $cache->delete($k ), 'successfully deleted the key');

T::ok( ! $cache->replace( $k, 1, 10), 'replace fails after key deletion');
T::ok( $cache->add( $k, 1, 10), 'add works after key deletion');
T::ok( $cache->replace( $k, 1, 10), 'replace works after key is added');

$k = 'wukka/cache/test/' . microtime(TRUE) . '/' . mt_rand(1, 10000);
T::ok( $cache->get( $k ) === NULL, 'cache get on a non-existent key returns NULL (not false)');

$k = 'wukka/cache/test/' . microtime(TRUE) . '/' . mt_rand(1, 10000);

T::is( $cache->increment($k, 1), FALSE, 'increment a new key returns (bool) FALSE');
T::is( $cache->decrement($k, 1), FALSE, 'decrement a new key returns (bool) FALSE');

T::cmp_ok( $cache->set( $k, 'test' ), '===', TRUE, 'setting a key returns bool TRUE');
T::cmp_ok( $cache->replace( $k, 'test1' ), '===', TRUE, 'replacing a key returns bool TRUE');
T::cmp_ok( $cache->{$k} = '11', '===', '11', 'setting using the magic method property approach returns value');
unset( $cache->{$k} );
T::cmp_ok( $cache->add($k, 'fun'), '===', TRUE, 'adding a key returns (bool) TRUE');

T::is( $cache->set( $k, NULL ), TRUE, 'setting a key to null returns true');
T::cmp_ok( $cache->get( array( $k ) ), '===', array(), 'after setting the key to null, key is deleted');


T::is( $cache->set( $k, $v = '0' ), TRUE, 'setting a key to zero returns true');
T::cmp_ok( $cache->get( $k ), '===', $v, 'after setting the key to 0, get returns zero value');
T::cmp_ok( $cache->get( array( $k ) ), '===', array($k=>$v), 'multi-get returns the key with zero value');

T::ok( $cache->set( $k, 1, $ttl = (3600 * 24 * 30)), 'setting with a huge timeout');
T::cmp_ok( strval($cache->get( $k )), '===', '1', 'get returns correct value');

$incr = 1000000;

T::ok( $cache->increment( $k, $incr), 'incrementing with a large number');
    
T::cmp_ok( strval($cache->get( $k )), '===', strval($incr + 1), 'get returns correct value');

T::ok( $cache->decrement( $k, $incr), 'decrementing with a large number');
T::cmp_ok( intval($cache->get( $k )), '===', 1, 'get returns correct value');

$huge_number = 9223372036854775806;

if( ! is_int( $huge_number ) ) $huge_number = 2147483646;

T::Debug( "testing with $huge_number" );

T::ok( $cache->set( $k, $v = $huge_number), 'setting a huge number');
T::cmp_ok( strval($cache->get( $k )), '===', strval($v), 'get returns correct value');

$v = $v + 1;
T::cmp_ok( strval($cache->increment($k, 1)), '===', strval($v),  'increment a huge number by 1');
T::cmp_ok( strval($cache->get( $k )), '===', strval( $v ), 'get returns correct value');

$cache->set( $k, $v);

$v = $v - 1;
T::cmp_ok( strval($cache->decrement($k, 1)), '===', strval($v),  'decrement a huge number by 1');
T::cmp_ok( strval($cache->get( $k )), '===', strval( $v ), 'get returns correct value');

$k = 'wukka/cache/test/' . microtime(TRUE) . '/' . mt_rand(1, 10000);
$v = '我能吞下玻璃而不傷身體';
T::ok( $cache->set( $k, $v), 'setting a string with utf-8 chars in it');
T::cmp_ok( strval($cache->get( $k )), '===',  $v, 'get returns correct value');

T::ok( $cache->delete( $k ), 'deleting the key');
T::cmp_ok( $cache->get( $k ), '===',  NULL, 'after deleting, get returns NULL');
