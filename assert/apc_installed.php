<?php
use Wukka\Test as T;
if( ! function_exists('apc_fetch') || ! ini_get('apc.enable_cli')) {
    T::plan('skip_all', 'php5-apc extension not installed or enabled (check apc.enable_cli=1)');
}
