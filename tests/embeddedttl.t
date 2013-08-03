#!/usr/bin/env php
<?php
include __DIR__ . '/../autoload.php';
$cache = new Wukka\Store\EmbeddedTTL(new Wukka\Store);
include __DIR__ . '/generic_tests.php';