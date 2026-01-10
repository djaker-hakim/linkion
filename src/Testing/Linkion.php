<?php

    namespace Linkion\Testing;

/**
 * remote class for the LinkionTestCase class
 */
class Linkion {

    /**
     * linkion testing var
     * @var LinkionTestCase
     */
    protected static LinkionTestCase $LINKION;


    public static function __callStatic($method, $args){
        static::$LINKION = new LinkionTestCase();
        return static::$LINKION->$method(...$args);
    }



}