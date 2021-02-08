<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;

class InstallPassport extends Command
{
    use PrettyCommandOutput;

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dwfw:install-passport
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Passport and outputs files for beginning .';

    /**
     * Execute the console command.
     *
     * @return mixed Command-line output
     */
    public function handle() :void
    {
        $this->progressBar = $this->output->createProgressBar(5);
        $this->progressBar->minSecondsBetweenRedraws(0);
        $this->progressBar->maxSecondsBetweenRedraws(120);
        $this->progressBar->setRedrawFrequency(1);

        $this->progressBar->start();

        $this->info(' DWFW passport installation started. Please wait...');
        $this->progressBar->advance();

        $this->line(' Installing Laravel passport - This can take a few minutes.');
        $this->executeProcess('composer require laravel/passport', 'Requiring laravel passport', 'laravel passport loaded');

        if(!$this->confirm(' Following publishes will overwrite every passport files. This command should be only used for new passport installs. Are you sure you want to continue?')){
            $this->info(' Passport install cancelled');
            exit;
        }

        $this->executeArtisanProcess('passport:install', [
            '--force' => '--force',
        ]);

        $this->line(' Publishing DWFW passport files');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'passport',
            '--force' => '--force',
        ]);

        $this->progressBar->finish();
        $this->warn(' Don\'t forget to add HasApiTokens Trait and Uncomment Notification functions in your User model!');
        $this->info(' DWFW installation finished.');
    }
}
