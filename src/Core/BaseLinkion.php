<?php

namespace Linkion\Core;

use Illuminate\Support\Facades\File;
use ReflectionClass;

class BaseLinkion {

    public array $list;

    protected $path;

    protected $baseNamespace = 'App\\View\\Components';

    public function __construct(){
        $this->path = app_path('View/Components');
        $this->scan();
    }

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

    public function register($componentName, $class){
        $ref = new ReflectionClass($class);
            
        if ($ref->isSubclassOf(LinkionComponent::class) && !$ref->isAbstract()) {
            $this->list[$componentName] = $class;
        }
    }

    public function getComponents(): array{
        return $this->list;
    }

    public function getComponent($name): string{
        return $this->list[$name];
    }

    public function getComponentClass($class): ?string{
        if(in_array($class, $this->list)) return $class;
        return null;
    }

    public function hasComponent($name): bool{
        return array_key_exists($name, $this->list);
    }


}