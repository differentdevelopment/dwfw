<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;

class Install extends Command
{
    use PrettyCommandOutput;

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dwfw:install
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Different Web FrameWork requirements on dev, publish files.';

    /**
     * Execute the console command.
     *
     * @return mixed Command-line output
     */
    public function handle()
    {
        $this->progressBar = $this->output->createProgressBar(5);
        $this->progressBar->minSecondsBetweenRedraws(0);
        $this->progressBar->maxSecondsBetweenRedraws(120);
        $this->progressBar->setRedrawFrequency(1);

        $this->progressBar->start();

        $this->info(' DWFW installation started. Please wait...');
        $this->progressBar->advance();

        $this->line(' Publishing config for Permission');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            '--tag' => 'config',
        ]);
//        $this->line(' Publishing for PermissionManager');
//        $this->executeArtisanProcess('vendor:publish', [
//            '--provider' => 'Backpack\PermissionManager\PermissionManagerServiceProvider',
//        ]);
        $this->line(' Publishing DWFW');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'base',
            '--force' => '--force',
        ]);

        $this->line(" Running DWFW seeders");
        $this->executeArtisanProcess('db:seed', [
            '--class' => 'Different\\Dwfw\\database\\seeds\\DwfwSeeder',
        ]);

        $this->info(' DWFW installation finished.');

        $this->line(' Publishing Account related files');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'account',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }
}
