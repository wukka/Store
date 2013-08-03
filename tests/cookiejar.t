#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\CookieJar(array('name'=>'fun', 'secret'=>'test'));
include __DIR__ . '/generic_tests.php';
