#!/usr/bin/env php
<?php
$file =__DIR__ . '/wukka-store.phar';
@unlink($file . '.tar.gz');
$phar = new Phar($file);
$phar->buildFromDirectory(__DIR__ . '/lib/');
$phar->convertToExecutable(Phar::TAR, Phar::GZ);
