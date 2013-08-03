#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$a = array();
$cache = new Store\Ref($a);
include __DIR__ . '/generic_tests.php';
