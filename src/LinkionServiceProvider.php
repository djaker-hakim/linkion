<?php

namespace Linkion;

use Linkion\Console\LinkionComponentMakeCommand;
use Illuminate\Support\ServiceProvider;
use Linkion\Console\LinkionCacheCommand;
use Linkion\Console\LinkionClearCommand;
use Linkion\Core\Linkion;
use Linkion\Core\LinkionBladeDirectives;

class LinkionServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // load linkion routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        // loading linkion script
        $this->loadViewsFrom([__DIR__ . '/views'], 'linkion');
       
        // loading linkion directives
        LinkionBladeDirectives::setup();

        // loading linkion arisan commands
        $this->commands([
            LinkionComponentMakeCommand::class,
            LinkionCacheCommand::class,
            LinkionClearCommand::class
        ]);
    
    }
}
