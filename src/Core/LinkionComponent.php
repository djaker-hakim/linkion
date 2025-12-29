<?php

namespace Linkion\Core;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ReflectionClass;

class LinkionComponent extends Component
{

    public $_id;
    public $_data;

    public $ref;

    public $componentCached=true;


    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        
    }

    protected function component($view){

        $this->_id ??= 'link_' . uniqid();
        $this->getData();
        return view($view);        
    }

    protected function getData(){
        $this->_data = json_encode($this->getProps());
    }

    public function getProps(){
        $props=[];
        $ref = new ReflectionClass(static::class);
        foreach($ref->getProperties() as $property){
            if(
                $property->isPublic() && 
                !in_array(
                    $property->getName(), 
                    ['_data', 'attributes']
                ) 
            ){
                $props[$property->getName()] = $property->getValue($this);
            }
        }
        return $props;
    }

    public function render(){}

}