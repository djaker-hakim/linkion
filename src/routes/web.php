<?php

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Route;
use Linkion\Core\Linkion;

// route for linkion script
Route::get('/linkion/script', function () {
    
    $file = dirname(__DIR__).'/js/main.min.js';
    abort_unless(file_exists($file), 404);

    return response()->file($file, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=604800',
    ]);
    
});
// route for the linkion and alpine script
Route::get('/linkionWithAlpine/script', function () {
    
    $file = dirname(__DIR__).'/js/main.alpine.min.js';
    abort_unless(file_exists($file), 404);

    return response()->file($file, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'public, max-age=604800',
    ]);
    
});

// route for the linkion frontend and backend connection 
Route::post('/linkion/connection', function (Request $request): array|bool|string {

        
    $linkion = new Linkion;
    $action = $request->input('action', '');
    $props = $request->input('props', []);
    $method = $request->input('method', []);

    // get listeners
    if($action == 'getListeners') return $linkion->getListeners();

    // upload a file 
    if($action == 'upload'){
        $props = json_decode($props, true);
        $files = $request->file($props['prop']);
        if(is_array($files)){
            $newFile = [];
            foreach($files as $file){
                $newFile[] = Linkion::fileUpload($file);
            }
        }else{
            $newFile = Linkion::fileUpload($files);
        }
        $newProps = [
            'componentName' => $props['componentName'],
            'ref' => $props['ref'],
            $props['prop'] => $newFile
        ];
        return json_encode(['props' => $newProps, 'result' => null ]);
    }
        
    // check for component
    if(!$linkion->hasComponent($props['componentName'])) return json_encode($props['componentName']." is not a linkion component");

    // make and sync the component
    $linkion->make($props);
    
    // run the logic
    $result = null;
    $template = null;

    // check for any middlewares
    $pipeline = new Pipeline(app());
    $pipeline->send($request)
    ->through($linkion->getTargetMiddleware($method['name']))
    ->then(function() use ($method, $action, &$template, &$result, &$linkion) {
        if($action == 'render' || !$linkion->component->componentCached){
            $template = $linkion->run($method['name'])
            ->with($linkion->component->data())
            ->render();
        }else{
            $result = $linkion->run($method['name'], $method['args']);
        }   
    });
            
    $newProps = $linkion->getProps();

    // check for dispatched events
    $events = $linkion->getDispatchedEvents();

        
    return json_encode([
        'props' => $newProps,
        'events' => $events ?: [],
        'template' => $template ,
        'result' => $result 
    ]);

})->middleware('web');
