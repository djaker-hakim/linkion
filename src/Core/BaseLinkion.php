<?php

namespace Linkion\Core;

use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * this class is responsible for getting the linkion components
 */
class BaseLinkion {

    /**
     * list of linkion components
     * @var array
     */
    public array $list;

    /**
     * path of linkion components
     * @var 
     */
    protected $path;

    /**
     * @var LinkionCache
     */
    protected LinkionCache $cache;

    /**
     * cached components and listeners list
     * @var array
     */
    public $cacheList;

    /**
     * base namespace of linkion components
     * @var string
     */
    protected string $baseNamespace = 'App\\View\\Components';

    public function __construct(){
        $this->path = app_path('View/Components');
        $this->cache = new LinkionCache;
        $this->init();
    }

    /**
     * init the linkion components
     * @return void
     */
    protected function init(){
        if(is_file($this->cache->getCachePath())){
            $this->cacheList = require $this->cache->getCachePath();
            $this->list = $this->cacheList['components'];
            return;
        }
        $this->scan();    
    }

    /**
     * scan the path of linkion components
     * @return void
     */
    public function scan(){
        
        foreach (File::allFiles($this->path) as $file) {
            $relative = str_replace(['/', '.php'], ['\\', ''], $file->getRelativePathname());
            $class = $this->baseNamespace . '\\' . $relative;

            // get the componentName
            $relativePath = str_replace('.php', '', $file->getRelativePathname());
            $relativePath = ltrim($relativePath, '/');
            $componentName = strtolower(str_replace('\\', '.', $relativePath));


            // check if subclass of LinkionComponent
            if (!class_exists($class)) continue;

            // register component
            $this->register($componentName, $class);
            
        }
    }

    /**
     * register the linkion components
     * @param string $componentName
     * @param string $class
     * @return void
     */
    public function register(string $componentName, string $class): void{
        $ref = new ReflectionClass($class);
            
        if ($ref->isSubclassOf(LinkionComponent::class) && !$ref->isAbstract()) {
            $this->list[$componentName] = $class;
        }
    }

    /**
     * get the linkion components
     * @return array
     */
    public function getComponents(): array{
        return $this->list;
    }

    /**
     * get a linkion component based on it's name
     * @param string  $name
     * @return mixed|string
     */
    public function getComponent(string $name): string{
        return $this->list[$name];
    }

    /**
     * get a linkion component based on it's class name
     * @param string  $name
     * @return mixed|string
     */
    public function getComponentClass($class): ?string{
        if(in_array($class, $this->list)) return $class;
        return null;
    }

    /**
     * check if component exists
     * @param string $name
     * @return bool
     */
    public function hasComponent(string $name): bool{
        return array_key_exists($name, $this->list);
    }


}