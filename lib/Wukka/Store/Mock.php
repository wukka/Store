<?php
namespace Wukka\Store;

class Mock extends Wrap {

    protected static $data;
    
    public function __construct(){
        if( ! isset( self::$data ) ) self::$data = new EmbeddedTTL( new \Wukka\Store );
        parent::__construct( self::$data );
    } 
}