#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;


$plan_ct = 23;

$internal = new Store\EmbeddedTTL();

$closures = array();

$closures['set'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->set($k, $v, $ttl ); };
$closures['add'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->add($k, $v, $ttl ); };
$closures['replace'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->replace($k, $v, $ttl ); };
$closures['get'] = function( $input) use( $internal ){ return $internal->get($input ); };
$closures['increment'] = function( $k, $v = 1 ) use( $internal ){ return $internal->increment($k, $v ); };
$closures['decrement'] = function( $k, $v = 1 ) use( $internal ){ return $internal->decrement($k, $v ); };
$closures['flush'] = function() use( $internal ){ return $internal->flush(); };
$closures['delete'] = function($k) use( $internal ){ return $internal->delete($k); };
$closures['ttlenabled'] = function() use ($internal ){ return $internal->ttlEnabled(); };


$cache = new Store\Closure( $closures );

include __DIR__ . '/generic_tests.php';




$internal = new Store\EmbeddedTTL();

$closures = array();

$closures['set'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->set($k, $v, $ttl ); };
$closures['add'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->add($k, $v, $ttl ); };
$closures['replace'] = function( $k, $v, $ttl = 0 ) use( $internal ){ return $internal->replace($k, $v, $ttl ); };
$closures['get'] = function( $input) use( $internal ){ return $internal->get($input ); };
$closures['increment'] = function( $k, $v = 1 ) use( $internal ){ return $internal->increment($k, $v ); };
$closures['decrement'] = function( $k, $v = 1 ) use( $internal ){ return $internal->decrement($k, $v ); };
$closures['flush'] = function() use( $internal ){ return $internal->flush(); };
$closures['delete'] = function($k) use( $internal ){ return $internal->delete($k); };
$closures['ttlenabled'] = function() use ($internal ){ return $internal->ttlEnabled(); };

$cache = new Store\Closure( array() );

$key = 'test1';

T::is( $cache->set( $key, 1 ), FALSE, 'set returns false with no closure attached');
$cache->attach( 'set', $closures['set'] );
T::is( $cache->set( $key, 1 ), TRUE, 'set returns true with a closure attached');
T::is( $internal->get( $key ), 1, 'internal object got the data');
T::is( $cache->get( $key ), FALSE, 'closure object get returns false with no closure attached');
$cache->attach( 'get', $closures['get'] );
T::is( $cache->get( $key ), 1, 'get returns 1 with a closure attached');
T::is( $cache->delete( $key ), TRUE, 'delete returns true even without a closure (uses set)');
T::cmp_ok( $cache->get( array( $key ) ), '===', array(), 'after deleting get returns NULL');

$internal->set($key, 1);

$cache = new Store\Closure( array() );

T::is( $cache->increment( $key), FALSE, 'increment returns false with no closure attached');
$cache->attach('increment', $closures['increment']);

T::is( $cache->increment( $key), 2, 'increment returns 2 with the closure attached');
T::is( $cache->increment( $key, 2), 4, 'increment by 2 returns 4');

T::is( $cache->decrement( $key ), FALSE, 'decrement returns false with no closure attached');
$cache->attach('decrement', $closures['decrement']);
T::is( $cache->decrement( $key, 2), 2, 'decrement by 2 returns 2');

$cache = new Store\Closure( array() );

T::is( $cache->replace( $key, 1 ), FALSE, 'replace returns false with no closure attached');
$cache->attach( 'replace', $closures['replace'] );
T::is( $cache->replace( $key, 5), TRUE, 'replace returns true with a closure attached');
T::is( $internal->get( $key ), 5, 'internal cache object attached to closure reflects new value');


$cache = new Store\Closure( array() );

$internal->delete( $key );

T::is( $cache->add( $key, 1 ), FALSE, 'add returns false with no closure attached');
$cache->attach( 'add', $closures['add'] );
T::is( $cache->add( $key, 5), TRUE, 'add returns true with a closure attached');
T::is( $internal->get( $key ), 5, 'internal cache object attached to closure reflects new value');


$cache = new Store\Closure( array() );
$cache->flush();
T::is( $internal->get($key) , 5, 'flush doesnt work with no closure attached');
$cache->attach( 'flush', $closures['flush'] );
$cache->flush();

T::is( $internal->get($key), NULL, 'after attaching closure, works fine');

$cache = new Store\Closure( array() );
T::is( $cache->ttlEnabled(), FALSE, 'with no closure, ttlenabled returns false');
$cache->attach( 'ttlenabled', $closures['ttlenabled'] );
T::is( $cache->ttlEnabled(), TRUE, 'after attaching closure, ttlenabled returns true');

$call_triggered = FALSE;
$cache = new Store\Closure( array('__call' =>function($method, array $args) use ( & $call_triggered ){ $call_triggered = TRUE; }) );
$cache->call_undefined_function();
T::ok( $call_triggered, '__call closure triggered properly');