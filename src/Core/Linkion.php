<?php

namespace Linkion\Core;

use ReflectionClass;

class Linkion extends BaseLinkion {


    use LinkionUpload;

    protected $component;

    protected ReflectionClass $reflector;

    public function make($props){
        $this->reflector = new ReflectionClass(
            $this->getComponent($props['componentName'])
        );
        $constructor = $this->reflector->getConstructor();
        $args = [];
        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                
                array_key_exists($name, $props) ?
                $args[] = $props[$name] : // Use the value from $props if it exists
                $args[] = null; // Otherwise, set null or handle missing
            }
        }
        
        // instantiate component with matched arguments
        $this->component = $this->reflector->newInstanceArgs($args);
        // dd($args);
        $this->sync($props);
        return $this;
    }

    public function sync($props): ?static{
        if(!$this->component) return null;
        foreach($props as $prop => $value){
            $property = $this->reflector->getProperty($prop);
            if(is_array($value)){
                if(static::isLnknFile($value)){
                    $value = static::getUploadedFile($value);
                }
                if(static::isLnknFiles($value)){
                    $value = static::getUploadedFiles($value);
                }
            }
            $property->setValue($this->component, $value);
        }
        return $this;
    }

    public function getProps(){
        $props = [];
        foreach($this->reflector->getProperties() as $property){
            if(
                $property->isPublic() && 
                !in_array(
                    $property->getName(), 
                    ['_data', 'attributes']
                ) 
            ){
                $props[$property->getName()] = $property->getValue($this->component);
            }
        }
        
        return $props;
    }

    public function run($method, $args = []){
        return $this->component->$method(...$args);
    }

    
}