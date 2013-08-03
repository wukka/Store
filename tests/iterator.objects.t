#!/usr/bin/env php
<?php
include_once __DIR__ . '/../autoload.php';

$input = array('a'=>new Wukka\Store\Iterator(), 'b'=>new stdclass, 'c'=> new ArrayIterator( array(1,2,3) ) );
include __DIR__ . DIRECTORY_SEPARATOR . 'iterator.base.php';
