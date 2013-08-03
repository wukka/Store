#!/usr/bin/env php
<?php
ob_start();
include __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store;
$cache = new Store\Cookie();
include __DIR__ . '/generic_tests.php';