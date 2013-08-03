#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Stat( new Store(), new Store);
include __DIR__ . '/generic_tests.php';