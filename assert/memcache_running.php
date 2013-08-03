<?php
use Wukka\Test as T;

if( ! @fsockopen('127.0.0.1', '11211') ){
    T::plan('skip_all', 'memcache not running on 127.0.0.1:11211');
}
