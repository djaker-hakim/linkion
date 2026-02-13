<?php

    namespace Linkion\Testing;

use Linkion\Core\BaseLinkion;
use Linkion\Core\Exceptions\LinkionException;
use Linkion\Core\LinkionComponent;
use ReflectionClass;

/**
 * class for testing linkion components
 */
class LinkionTestCase extends BaseLinkion {


    /**
     * reflector for linkion components
     * @var ReflectionClass
     */
    protected ReflectionClass $reflector;
    /**
     * linkion component class string 
     * @var string
     */
    protected string $class;

    /**
     * linkion component instance
     * @var LinkionComponent
     */
    protected LinkionComponent $component;

    /**
     * init the linkion component
     * @param string $component
     * @param array $args
     * @throws LinkionException
     * @return static
     */
    public function test(string $component, array $args = []){

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

    /**
     * set property to linkion component instance
     * @param mixed $prop
     * @param mixed $value
     * @return static
     */
    public function setProperty($prop, $value): static{

        if($this->reflector->hasProperty($prop)){
            $property = $this->reflector->getProperty($prop);
            $property->setValue($this->component, $value);
        }
        return $this;
    }

    /**
     * sets all the propeties $props given
     * @param array $props
     * @return LinkionTestCase
     */
    public function setProperties(array $props): static{
        foreach($props as $prop => $value){
            $this->setProperty($prop, $value);
        }
        return $this;
    }

    /**
     * get the property from the linkion component
     * @param string $prop
     */
    public function getProperty(string $prop): mixed{
        if($this->reflector->hasProperty($prop)){
            $property = $this->reflector->getProperty($prop);
            return $property->getValue($this->component);
        }
        return null;
    }

    /**
     * get all properties of linkion component
     * @return array
     */
    public function getProperties(){
        $props = [];
        foreach($this->reflector->getProperties() as $property){
            
            $props[$property->getName()] = $property->getValue($this->component);
            
        }
        
        return $props;
    }

    /**
     * run the linkion component method and return it's result
     * @param mixed $method
     * @param mixed $args
     */
    public function run(string $method, array $args = []){
        return $this->component->$method(...$args);
    }

    
    /**
     * run the linkion componet method but does not return the result of it
     * @param string $method
     * @param array $args
     * @return LinkionTestCase
     */
    public function runSilently(string $method, array $args = []): static{
        $this->run($method, $args);
        return $this;
    }

}

