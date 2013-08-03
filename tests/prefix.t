#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Prefix(new Store\EmbeddedTTL(new \Wukka\Store), 'prefixtesting/');
include __DIR__ . '/generic_tests.php';