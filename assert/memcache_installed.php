<?php
use Wukka\Test as T;

if( ! class_exists('Memcache') && ! class_exists('Memcached') ){
    T::plan('skip_all', 'no pecl-memcache or pecl-memcached extension installed');
}