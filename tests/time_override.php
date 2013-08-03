<?php
namespace Wukka\Store;

function time() { 
  return \time() + $_SERVER['TIME_OFFSET']; 
}

if( ! isset ($_SERVER['TIME_OFFSET'] ) ) $_SERVER['TIME_OFFSET'] = 0;