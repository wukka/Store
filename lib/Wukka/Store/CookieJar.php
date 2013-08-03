<?php
namespace Wukka\Store;
use Wukka\Exception;
use Wukka\Container;

class CookieJar extends Wrap implements Iface {

    protected $config;
    protected $ob = FALSE;
    protected $checksum = NULL;
    
    public function __construct( $config = NULL ){
        if( headers_sent() ) throw new Exception('headers sent, cannot store');
        $cookiejar = $this;
        ob_start(function( $output )use( $cookiejar ){ $cookiejar->__write(); return $output;  } );
        $this->ob = TRUE;
        $config = $config instanceof Iface ? $config : new \Wukka\Store( $config );
        if( ! isset( $config->name ) ) $config->name = md5(get_class( $this ));
        if( ! isset( $config->path ) ) $config->path = '/';
        // if you want the data to be secure and encrypted consider using Wukka\Crypto
        // if you want to make sure no one tampers with the data, you can checksum it pretty easily.
        
        if( ! $config->serializer instanceof \Closure ){
            $config->serializer = function( $method, $input ) {
                if( $method == 'serialize') return base64_encode( serialize( $input ) );
                if( $method == 'unserialize') return unserialize( base64_decode( $input ));
            };
            if( $config->secret ){
                $serializer = $config->serializer;
                $secret = $config->secret;
                $config->serializer = function( $method, $input ) use( $serializer, $secret ) {
                    if( $method == 'serialize' ){
                        $input = $serializer($method, $input );
                        return sha1( $secret . $input ) . '.' . $input;
                    }
                    if( $method == 'unserialize'){
                        if(! is_scalar( $input ) ) return NULL;
                        if( strpos( $input, '.' ) === FALSE ) return NULL;
                        list($sig, $input) = explode('.', $input, 2);
                        if ($sig !== sha1( $secret . $input)) return NULL;
                        return $serializer('unserialize', $input );
                    }
                };
            }
        }
        $this->config = $config;
        $key = $config->name;
        $v = isset( $_COOKIE[ $key ] ) ? $_COOKIE[ $key ] : NULL;
        $this->checksum = sha1( $v );
        $serializer = $config->serializer;
        $data = $serializer('unserialize', $v);
        parent::__construct( new Iterator( $data ) );
    }
    
    public function add( $key, $value, $expires = 0){
        $res = $this->core->add( $key, $value, $expires );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function set( $key, $value, $expires = 0){
        $res = $this->core->set( $key, $value, $expires );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function replace( $key, $value, $expires = 0){
        $res = $this->core->replace( $key, $value, $expires );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function increment( $key, $value = 1 ){
        $res = $this->core->increment( $key, $value );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function decrement( $key, $value = 1 ){
        $res = $this->core->decrement( $key, $value );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function delete( $key ){
        $res = $this->core->delete( $key );
        if( ! $res ) return $res;
        $this->__save();
        return $res;
    }
    
    public function flush(){
        $res = $this->core->flush();
        $this->__save();
        return $res;
    }
    
    public function __destruct(){
        if( $this->ob ) {
            ob_end_flush();
        }
        $this->__write();

    }
    
    public function __write(){
        if( ! $this->ob ) return;
        $this->ob = FALSE;
        $c = $this->config;
        if( headers_sent() ) throw new Exception('headers sent, could not store');
        $serializer = $c->serializer;
        
        $v = $serializer('serialize', $this->all());
        $key = $c->name;
        if( sha1( $v ) == $this->checksum ) return;
        if( $v !== NULL) {
            setcookie($key, $_COOKIE[ $key ] = $v, $c->ttl, $c->path, $c->domain, $c->secure, $c->httponly);
        } else {
            unset( $_COOKIE[ $key ] );
            setcookie($key, '', 0, $c->path, $c->domain, $c->secure, $c->httponly);
        }
        return $v;
    }
    
    public function __save(){
        $serializer = $this->config->serializer;
        $_COOKIE[ $this->config->name ] = $serializer('serialize', $this->all());
    }
    
    public function ttlEnabled(){
        return FALSE;
    }
}