<?php

namespace Linkion;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
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
    }
}
