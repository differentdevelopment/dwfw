<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;

class Upgrade extends Command
{

    use PrettyCommandOutput;

    protected $finish_message = ' Dwfw is already upgraded to the most recent version'; //Needs to be changed in the upgrade methods

    /**
     * Array of methods used for upgrading to the given version
     *
     * @var array
     */

    protected $upgrade_methods = [
        '0.10.14' => 'upgrade_to_0_10_14',
        '0.10.15' => 'upgrade_to_0_10_15',
    ];

    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dwfw:upgrade
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade Different Web FrameWork requirements on dev, publish files.';

    /**
     * Execute the console command.
     *
     * @return mixed Command-line output
     */
    public function handle()
    {
        foreach ($this->upgrade_methods as $version => $upgrade) {
            if ($version > config('dwfw.version') ?? '0.10.13') {
                if (is_callable([$this, $upgrade])) {
                    $this->$upgrade();
                } else {
                    $this->error(' Error while upgrading, missing upgrade method');
                    exit;
                }
            }
        }
        $this->info($this->finish_message);
    }

    private function start_progress_bar($version, int $max = 5, int $min_sec_between_redraws = 0, int $max_sec_between_redraws = 120, int $redraw_frequency = 1)
    {
        $this->progressBar = $this->output->createProgressBar($max);
        $this->progressBar->minSecondsBetweenRedraws($min_sec_between_redraws);
        $this->progressBar->maxSecondsBetweenRedraws($max_sec_between_redraws);
        $this->progressBar->setRedrawFrequency($redraw_frequency);

        $this->progressBar->start();

        $this->info(' Upgrading to '.$version.'. Please wait...');
        $this->progressBar->advance();
    }

    private function finish_progress_bar()
    {
        $this->progressBar->finish();
    }

    private function upgrade_to_0_10_14()
    {
        $this->start_progress_bar('0.10.14', 5);

        $this->line(' Publishing CheckIpMiddleware');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'config.checkIp',
            '--force' => '--force',
        ]);

        $this->line(' Publishing Backpack translations');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.langs',
            '--force' => '--force',
        ]);

        $this->line(' Publishing new version number');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'config.dwfw',
            '--force' => '--force',
        ]);
        $this->finish_message = ' DWFW succesfully upgraded. New version: 0.10.14';
        $this->info(' DWFW upgrade to 0.10.14 finished.');
        $this->finish_progress_bar();
    }

    private function upgrade_to_0_10_15()
    {
        $this->start_progress_bar('0.10.15', 3);
        $this->line(' Publishing new version number');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'config.dwfw',
            '--force' => '--force',
        ]);
        $this->line(' Updating Backpack translations');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.langs',
            '--force' => '--force',
        ]);
        $this->finish_message = ' DWFW succesfully upgraded. New version: 0.10.15';
        $this->finish_progress_bar();
    }

}
