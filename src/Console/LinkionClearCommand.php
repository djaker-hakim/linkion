<?php

namespace Linkion\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Linkion\Core\LinkionCache;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'linkion:clear')]
class LinkionClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'linkion:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the linkion cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->getCachedComponentsPath());

        $this->components->info('Linkion cache cleared successfully.');
    }

    /**
     * get the cache path of linkion components
     * @return string 
     */
    protected function getCachedComponentsPath(){
        return (new LinkionCache())->getCachePath();
    }
}
