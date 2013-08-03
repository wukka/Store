#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
ob_start();

$limit = 10;


$cache = new Store\EmbeddedTTL;
T::plan(8);

$s = Store\Session::init( $o = new Store\Observe( $cache ) );
session_start();
$id = session_id();



T::is( session_id(), $id ,'session id working');

$_SESSION['test'] = 'foo';

session_write_close();

$calls = $o->calls();

$call = array_shift( $calls );

T::is( $call['method'], 'get', 'read handler called cache get');
T::like( $call['args'][0], "/$id/", 'read handler invoked get with the session id');
T::ok( ! $call['result'], 'no data returned');

$call = array_shift( $calls );
T::is( $call['method'], 'set', 'write handler called cache set');
T::like( $call['args'][0], "/$id/", 'write handler invoked set with the session id');

T::is( $call['args'][1]['data'], 'test|s:3:"foo";', 'serialized test=>foo written');

$call = array_shift( $calls );

T::is( $call['method'], 'add', 'write handler called add to lock the key');
