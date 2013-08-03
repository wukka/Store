<?php
namespace Wukka\Store;

class Disabled Implements Iface {

    function get( $request ){
        if( is_array( $request ) ) return array();
        return FALSE;
    }
    
    function set($k, $v ){
        return FALSE;
    }
    
    function add( $k, $v ){
        return FALSE;
    }
    
    function replace( $k, $v ){
        return FALSE;
    }
    
    function increment( $k, $step = 1){
        return FALSE;
    }
    
    function decrement( $k, $step = 1){
        return FALSE;
    }
    
    function delete( $k ){
        return FALSE;
    }

    public function flush(){
        return FALSE;
    }
    
    public function load( $input ){
        if( $input === NULL ) return;
        if( is_array( $input ) || $input instanceof Iterator ) {
            foreach( $input as $k=>$v ) $this->__set( $k, $v);
        }
    }
    public function __set( $k, $v ){
        return $this->set( $k, $v );
    }
    public function __get( $k ){
        return $this->get( $k );
    }
    public function __unset( $k ){
        return $this->delete( $k );
    }
    public function __isset( $k ){
        $v = $this->get( $k );
        if( $v === FALSE || $v === NULL ) return FALSE;
        return TRUE;
    }
    
    public function ttlEnabled(){
        return FALSE;
    }

}