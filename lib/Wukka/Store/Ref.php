<?php
namespace Wukka\Store;

class Ref extends \Wukka\Store {
    public function __construct( array & $data ){
        $this->__d =& $data;
    }

}