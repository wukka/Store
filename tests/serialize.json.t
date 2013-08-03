#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Serialize( new Store(), function( $method, $input ){
    if( $method == 'serialize' ) return @json_encode($input);
    if( $method == 'unserialize' ) return @json_decode($input, TRUE);
});
include __DIR__ . '/generic_tests.php';