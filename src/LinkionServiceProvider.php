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
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        LinkionBladeDirectives::setup();
        $this->commands([
            LinkionComponentMakeCommand::class,
            LinkionCacheCommand::class,
            LinkionClearCommand::class
        ]);
    
    }
}
