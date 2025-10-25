<?php

use App\View\Components\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Linkion\Core\Linkion;

Route::get('/linkion/script', function () {
    
    $file = dirname(__DIR__).'/js/main.min.js';
    abort_unless(file_exists($file), 404);

    return response()->file($file, [
        'Content-Type' => 'application/javascript',
        // 'Cache-Control' => 'public, max-age=604800',
    ]);
    
});

Route::post('/linkion/connection', function (Request $request) {
    
    $linkion = new Linkion;
    $actions = $request->actions ?? [];
    $props = $request->props;
    $methods = $request->methods ?? [];

    if($actions == 'upload'){
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
            $props['prop'] => $newFile
        ];
        return json_encode(['props' => $newProps, 'result' => null ]);
    }


    // check for component
    if(!$linkion->hasComponent($props['componentName'])) return json_encode("no component of this name");

    // make and sync the component
    $linkion->make($props);
    
    // run the logic
    $result = null;
    
    if(!empty($methods)){
        $result = $linkion->run($methods['method'], $methods['args']);
    }
    $template = null;
    if($actions == 'render' || !$linkion->component->componentCached){
        $template = $linkion->run('render')
        ->with($linkion->component->data())
        ->render();
    }
        
    $newProps = $linkion->getProps();
        
    return json_encode(['props' => $newProps,'template' => $template ,'result' => $result ]);

})->middleware('web');
