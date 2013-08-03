<?php
include_once __DIR__ . '/../autoload.php';
use Wukka\Test as T;
use Wukka\Store\Iterator as Container;
T::plan(22);
$c = new Container();
foreach( array('result_set', 'result_get', 'result_isset', 'result_unset') as $key ){
    $$key = array();
}
if( ! isset( $input ) || ! is_array( $input ) ) $input = array();
foreach( $input as $k=>$v ){
    $result_set[ $k ] = $c->$k = $v;
    $result_isset[ $k ] = isset( $c->$k );
    $result_get[ $k ] = $c->$k;
    unset( $c->$k );
    $result_unset[ $k ] = $c->$k;
}

T::is( $input, $result_set, 'set works properly' );
T::is( $input, $result_get, 'get works properly' );
T::is( array_fill_keys( array_keys( $input ), TRUE), $result_isset, 'isset works properly' );
T::is( array_fill_keys( array_keys( $input ), NULL), $result_unset, 'unset works properly' );
T::is( $c->non_existent, NULL, 'non-existent variables are null' );

$c->load( $input );
T::is( $c->get( array_keys($input) ), $input, 'multi-get works properly');
T::is( $c->all(), $input, 'grabbed all of the data at once');

$each = array();
while( list( $k, $v ) = $c->each()  ){
    $each[ $k ] = $v;
}
T::is( $c->all(), $each, 'each loop returns all the data in the container');
T::is( array_keys( $input ), $c->keys(), 'keys returns all the keys passed to input');

T::is( array_keys($input, 'a'), $c->keys('a'), 'search for a key');

T::is( $c->pop(), $v = array_pop($input), 'popped off an element, same as input');

T::is( $c->push($v), array_push($input, $v), 'pushed an element back onto the container');
T::is( $c->all(), $input, 'after pop and push, input matches container');

T::is( $c->shift(), $v = array_shift($input), 'shifted off an element, same as input');

T::is( $c->unshift($v), array_unshift($input, $v), 'unshift an element back onto the container');
T::is( $c->all(), $input, 'after shift and unshift, input matches container');
@asort( $input );
@$c->sort();
T::is( $c->all(), $input, 'after sorting, matches sorted input');
ksort( $input );
$c->ksort();
T::is( $c->all(), $input, 'after key sorting, matches sorted input');
krsort( $input );

T::is( $c->all(), $input, 'after reverse key sorting, matches sorted input');

$c->flush();
T::is( $c->all(), array(), 'flush removes everything from the container');
$c->load( $input );
T::is( $c->all(), $input, 'load puts it all back in again');

$c->push(0);
$c->push(NULL);
array_push( $input, 0);
array_push( $input, NULL);

T::is( $c->keys(NULL, TRUE), array_keys( $input, NULL, TRUE ), 'strict match works');
