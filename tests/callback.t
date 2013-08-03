#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Callback(new Store\EmbeddedTTL(new \Wukka\Store), array());
include __DIR__ . '/generic_tests.php';