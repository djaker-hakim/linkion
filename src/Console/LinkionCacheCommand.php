<?php

namespace Linkion\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;
use Linkion\Core\Linkion;
use Linkion\Core\LinkionCache;
use Symfony\Component\Console\Attribute\AsCommand;


#[AsCommand(name: 'linkion:cache')]
class LinkionCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linkion:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a linkion component cache file for faster component registration';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * the core linkion instance.
     * 
     */
    protected Linkion $linkion;
    
    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->linkion = new Linkion();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->callSilent('linkion:clear');

        $components = $this->getCachedComponents();

        if (count($components['components']) === 0) {
            return $this->components->error("Your application doesn't have any linkion components.");
        }

        
        $this->files->put(
            $this->getCachedComponentsPath(), $this->buildComponentsCacheFile($components)
        );

        $this->components->info('Linkion Components cached successfully.');
    }


    /**
     * get the linkion cache array
     * @return array
     */
    protected function getCachedComponents():array{
        return [
            "components" => $this->getComponents(),
            "listeners" => $this->getListeners()
        ];
    }


    /**
     * get the linkion components from the application
     *
     * @return array
     */
    protected function getComponents(): array
    {
        return $this->linkion->getComponents();
    }

    /**
     * get the linkion components listeners 
     *
     * @return array
     */
    protected function getListeners(): array
    {
        return $this->linkion->getListeners();
    }

    /**
     * get the cache path of linkion components
     * @return string 
     */
    protected function getCachedComponentsPath(){
        return (new LinkionCache())->getCachePath();
    }

    
    /**
     * Build the route cache file.
     *
     * @param array  $components
     * @return string
     */
    protected function buildComponentsCacheFile(array $components): string
    {
        $stub = $this->files->get(__DIR__.'/stubs/linkion-components.stub');

        return str_replace('{{ components }}', var_export($components, true), $stub);
    }
}
