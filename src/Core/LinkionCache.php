<?php

namespace Linkion\Core;


class LinkionCache {


    /**
     * path of the linkion cache file
     * @var 
     */
    protected $path;

    /**
     * name of the cache file
     */
    protected $name = 'linkion';

    public function __construct()
    {
        $this->getCachePath();
    }

    /**
     * return the cache path of cache file;
     * @return string
     */
    public function getCachePath(){
        $this->path ?:
        $this->path = base_path('bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $this->name.'.php');
        return $this->path;
    }

    /**
     * return the cache file name;
     * @return string
     */
    public function getCacheName(){
        return $this->name;
    }


}