<?php

    namespace Linkion\Testing;

    use Linkion\Core\BaseLinkion;
use Linkion\Core\Exceptions\LinkionException;
use ReflectionClass;

class LinkionTestCase extends BaseLinkion {


    protected ReflectionClass $reflector;
    protected $class;

    protected $component;

    public function test($component, $args = []){

        // see if the component or class exists
        if($this->getComponentClass($component)){
            $this->class = $component;
        }else if($this->hasComponent($component)) {
            $this->class = $this->getComponent($component);
        }else{
            throw new LinkionException("$component does not exist");
        }

        $this->reflector = new ReflectionClass($this->class);

        // instantiate component with matched arguments
        $this->component = $this->reflector->newInstanceArgs($args);
        return $this;
    }

    public function setProperty($prop, $value){

        if($this->reflector->hasProperty($prop)){
            $property = $this->reflector->getProperty($prop);
            $property->setValue($this->component, $value);
            return $this;
        }
    }

    public function setProperties($props){
        foreach($props as $prop => $value){
            $this->setProperty($prop, $value);
        }
        return $this;
    }

    public function getProperty($prop){
        if($this->reflector->hasProperty($prop)){
            $property = $this->reflector->getProperty($prop);
            return $property->getValue($this->component);
        }
    }

    public function getProperties(){
        $props = [];
        foreach($this->reflector->getProperties() as $property){
            
            $props[$property->getName()] = $property->getValue($this->component);
            
        }
        
        return $props;
    }

    public function run($method, $args = []){
        return $this->component->$method(...$args);
    }

    public function runSilently($method, $args = []): static{
        $this->run($method, $args);
        return $this;
    }

}

