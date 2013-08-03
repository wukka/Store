<?php
namespace Wukka\Store;

class Queue extends Wrap {

    protected $queue = array();
    
    
    public function queue( $keys, $options = NULL ){
        if( ! is_array( $keys ) ) $keys = array( $keys );
        if( ! $options instanceof Options ) $options = new Options( $options );
        foreach( $keys as $k ){
            $key = $options->prefix . $k;
            $this->queue[ $key ] = $options;
        }
    }
    
    public function fetchAll(){
        $queue = $this->queue;
        $this->queue = array();
        $res = $this->get( array_keys( $queue ) );
        if( ! is_array( $res ) ) return array();
        foreach( array_keys( $res) as $k) unset( $queue[ $k ]->missing_callback );
        $missing = $callbacks = array();
        foreach( $queue as $k=>$options ){
            if( isset( $options->missing_callback ) ){
                $cb_key = self::hashCallback( $options->missing_callback );
                if( ! isset( $callbacks[ $cb_key ] ) ) $callbacks[ $cb_key ] = $options->missing_callback;
                if( ! isset( $missing[ $cb_key ] ) ) $missing[ $cb_key ] = array();
                $missing[ $cb_key ][substr($k, strlen($options->prefix)) ] = $k;
            }
        }
        foreach( $missing as $cb_key => $keys ){
            $cb = $callbacks[ $cb_key ];
            $direct_res = call_user_func( $cb, array_keys($keys));
            if( ! is_array( $direct_res ) ) $direct_res = array();
            if( $options->cache_missing ){
                foreach($keys as $k ){
                    if( ! isset( $direct_res[ $k ] ) ) $direct_res[ $k ] = self::UNDEF; 
                }
            }
            foreach( $direct_res as $key=>$v ){
                $k = $keys[ $key ];
                $res[ $k ] = $v;
                $this->set( $k, $v, $queue[ $k ]->timeout );
            }
            if( isset( $options->default ) ) {
                foreach( $keys as $k ){
                    if( ! isset( $res[ $k ] ) ) $res[$k] = $options->default;
                }
            }
            
        }
        
        foreach( $res as $k=>$v){
            if( $v === self::UNDEF ){
                unset( $res[ $k ] );
                continue;
            }
            if( $queue[ $k ]->response_callback ) call_user_func( $queue[ $k ]->response_callback, substr($k, strlen($queue[$k]->prefix)), $v );
        }
        return $res;
    }
    
    protected static function hashCallback( $cb ){
        if( $cb instanceof \Closure ){
            $cb = new \ReflectionFunction( $cb );
            $cb = $cb->__toString();
        }elseif( is_array( $cb ) && isset( $cb[0] ) && is_object( $cb[0] ) ) $cb[0] = spl_object_hash( $cb[0] );
        
        return md5( serialize($cb) );
    }
}