<?php

    namespace Linkion\Testing;


class Linkion {

    protected static LinkionTestCase $LINKION;


    public static function __callStatic($method, $args){
        static::$LINKION = new LinkionTestCase();
        return static::$LINKION->$method(...$args);
    }



}