<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;

class Upgrade extends Command
{

    use PrettyCommandOutput;

    protected $finish_message = ' Dwfw is already upgraded to the most recent version';

    /**
     * Array of methods used for upgrading to the given version
     *
     * @var array
     */

    protected $upgrade_methods = [
        '0.10.14' => 'upgrade_to_0_10_14',
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
        $this->progressBar = $this->output->createProgressBar(5);
        $this->progressBar->minSecondsBetweenRedraws(0);
        $this->progressBar->maxSecondsBetweenRedraws(120);
        $this->progressBar->setRedrawFrequency(1);

        $this->progressBar->start();

        $this->info(' DWFW upgrade started. Please wait...');
        $this->progressBar->advance();

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

        $this->progressBar->finish();
        $this->info($this->finish_message);

    }

    private function upgrade_to_0_10_14()
    {

        $this->info(' Upgrading to 0.10.14');

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
        $this->finish_message=' DWFW succesfully upgraded. New version: 0.10.14';
        $this->info(' DWFW upgrade to 0.10.14 finished.');
    }

}
