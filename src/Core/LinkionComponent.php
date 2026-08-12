<?php

namespace Linkion\Core;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Linkion\Core\Exceptions\LinkionException;
use Linkion\Middleware\LinkionMiddlewareOptions;
use ReflectionClass;


class LinkionComponent extends Component
{
    /**
     * linkion component view id
     * @var string|null
     */
    public ?string $_id = null;
    /**
     * linkion component data
     * @var string
     */
    public string $_data;

    /**
     * linkion component ref (for multiple instances for the same component)
     * @var string
     */
    public $ref;

    /**
     * this is responsible for the frontend template caching 
     * @var bool 
     */
    public $componentCached=true;

    /**
     * this is the components middlewares
     * @var array
     */
    protected $middleware = [];

   
    /**
     * registers components middlewares
     * @param mixed $middleware
     * @param array $options
     * @return LinkionMiddlewareOptions
     */
    protected function middleware($middleware, array $options = []): LinkionMiddlewareOptions
    {
        foreach ((array) $middleware as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => &$options,
            ];
        }
        
        return new LinkionMiddlewareOptions($options);
    }

    public function getMiddleware(): array
    {        
       return $this->middleware;
    }

    
    public function init(){

    }


    /**
     * setup a linkion component view
     * @param string $view
     * @return View|\Illuminate\Contracts\View\Factory
     */
    protected function component(string $view): View|string{
        $this->init();
        $this->_id ??= 'link_' . uniqid();
        $this->getData();
        return view($view);        
    }

    /**
     * build the data needed for the component
     * @return void
     */
    protected function getData(){
        $this->_data = htmlspecialchars(json_encode($this->getProps()));
    }

    /**
     * get linkion component allowed properties
     * @return array
     */
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
            )
            {
                $type = $property->getType();
                // dd($type);
                if($type && !in_array($type->getName(),['int', 'string', 'array', 'bool', 'float'])){
                    throw new LinkionException("property type {$type} not supported");
                }
                
                $property->isInitialized($this) ?
                $props[$property->getName()] = $property->getValue($this) :
                $props[$property->getName()] = null;   
            }
        }
        return $props;
    }

    /**
     * store linkion events
     * @var array
     */
    protected array $events = [];

    /**
     * dispatch a frontend event
     * @param string $event
     * @param array $detail
     * @return void
     */
    protected function dispatch(string $event, array $detail = []){
        $this->events[] = [
            "name" => $event,
            "detail" => $detail
        ];
    }

    /**
     * get linkion events
     * @return array
     */
    public function getEvents(){
        return $this->events;
    }

    public function render(){}

}