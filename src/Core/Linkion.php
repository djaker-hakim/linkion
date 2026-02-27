<?php

namespace Linkion\Core;

use Linkion\Attributes\On;
use ReflectionClass;

/**
 * this class handles the requests comming from the frontend
 */
class Linkion extends BaseLinkion {


    use LinkionUpload;

    /**
     * linkion component instance
     * @var LinkionComponent
     */
    public LinkionComponent $component;

    /**
     * reflector class instance for handeling the component
     * @var ReflectionClass
     */
    protected ReflectionClass $reflector;

    /**
     * makes linkion component instance from $props
     * @param array $props
     * @return static
     */
    public function make(array $props){
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
        $this->sync($props);
        return $this;
    }

    /**
     * sync the frontend linkion component with backend linkion component
     * @param mixed $props
     * @return Linkion|null
     */
    public function sync($props): ?static{
        if(!$this->component) return null;
        foreach($props as $prop => $value){
            // check if class has property
            if(!$this->reflector->hasProperty($prop)) continue;
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

    /**
     * return array of linkion component properties
     * @return array
     */
    public function getProps(): array{
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

    /**
     * get applicable middleware on method
     * @param mixed $method
     * @return array
     */
    public function getTargetMiddleware($method)
    {
        $middleware = $this->component->getMiddleware();
        
        $applicable = [];
        
        foreach ($middleware as $m) {
            // Check if this middleware should run for this method
            if ($this->methodExcludedByOptions($method, $m['options'])) {
                continue;  // Skip this middleware
            }
            
            $applicable[] = $m['middleware'];
        }
        
        return $applicable;
    }

    /**
     * Check if middleware has method in options
     * @param mixed $method
     * @param mixed $options
     * @return bool
     */
    public function methodExcludedByOptions($method, $options){
        // If 'only' is set, check if method is in the list
        if (isset($options['only'])) {
            return !in_array($method, (array) $options['only']);
        }
        
        // If 'except' is set, check if method is in the list
        if (isset($options['except'])) {
            return in_array($method, (array) $options['except']);
        }
        
        // No restrictions, middleware applies
        return false;
    }

    /**
     * runs the linkion component method
     * @param string $method
     * @param array $args
     */
    public function run(string $method, array $args = []){
        return $this->component->$method(...$args);
    }

    /**
     * handle linkion events
     * @return array{detail: mixed, name: mixed[]}
     */
    public function getDispatchedEvents(): array{
        return $this->component->getEvents();
    }


    /**
     * return a array of linkion event listeners
     * @return array
     */
    public function getListeners(): array{
        // get the cached listeners
        if(($this->cacheList)) return $this->cacheList['listeners'];
        
        // scan for the listeners
        $listeners = [];
        $components = $this->getComponents();
        foreach($components as $componentName => $class){
            $reflection = new ReflectionClass($class);
            foreach ($reflection->getMethods() as $method) {
                $atts = $method->getAttributes(On::class);
                if($atts){
                    foreach($atts as $att){
                        $event = $att->getArguments()[0];
                        $listeners[] = [
                            'event' => $event,
                            'componentName' => $componentName,
                            'method' => $method->getName(),
                        ];
                    }
                }
                
            }
        }
        return $listeners;   
    }


    
}