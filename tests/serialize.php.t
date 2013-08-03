#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Serialize( 
    $core = new Store(), 
    function( $method, $input ){
        return $method( $input );
    }
);
include __DIR__ . '/generic_tests.php';
