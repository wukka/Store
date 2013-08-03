#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
$cache = new Wukka\Store\TTL();
include __DIR__ . '/generic_tests.php';