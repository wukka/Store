<?php
/**
 * @copyright 2003-present GAIA Interactive, Inc.
 */
namespace Wukka\Store;


// provide serialization mechanism if the internal store only deals with strings.
class Serialize extends Wrap { 
    protected $_s;   
        
    public function __construct( $core, \Closure $serializer = NULL ){
        if( ! $serializer ) {
            $serializer = function( $method, $input ){
                return $method( $input );
            };
        }
        $this->_s = $serializer;
        parent::__construct( $core );
    }

    public function get( $request ){
        if( is_scalar( $request ) ) {
            $res = $this->core->get( $request );
            if( $res === NULL || $res === FALSE ) return NULL;
            $res = $this->unserialize( $res );
            if( $res === NULL || $res === FALSE ) return NULL;
            return $res;
        }
        if( ! is_array( $request ) ) return NULL;
        if( count( $request ) < 1 ) return array();
        $res = $this->core->get( $request );
        if( ! is_array( $res ) ) return array();
        foreach($res as $key => $value ){
            if( $value === NULL || $value === FALSE ) continue;
            $value = $this->unserialize($value);
            if( $value === NULL || $value === FALSE ) continue;
            $res[ $key ] = $value;
        }
        return  $res;
    }
    
    public function add( $k, $v, $ttl = NULL ){
        return $this->core->add( $k, $this->serialize( $v ), $ttl );
    }
    
    public function set( $k, $v, $ttl = NULL ){
        if( $v === NULL ) return $this->core->delete( $k );
        return $this->core->set($k, $this->serialize($v), $ttl);
    }
    
    public function replace( $k, $v, $ttl = NULL ){
        return $this->core->replace( $k, $this->serialize( $v ), $ttl );
    }
    
    protected function serialize($v){
        $scalar = is_scalar( $v );
        $handler = $this->_s;
        if( ! $scalar || is_bool($v) ) return $handler(__FUNCTION__, $v);
        if( $scalar && ctype_digit( (string) $v ) ) return $v;
        return $handler(__FUNCTION__, $v);
    }
    
    protected function unserialize( $v ){
        if( $v === NULL ) return NULL;
        if( ! is_scalar( $v ) ) return $v;
        if(  ctype_digit( (string) $v ) ) return $v;
        $handler = $this->_s;
        return $handler(__FUNCTION__, $v);
    }
}