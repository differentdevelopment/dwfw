<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Upgrade extends Command
{

    use PrettyCommandOutput;

    const VERSION = '0.14.2';
    protected string $finish_message;
    /**
     * Array of methods used for upgrading to the given version
     * @var array
     */
    protected $upgrade_methods = [
        '0.10.14' => 'upgrade_to_0_10_14',
        '0.10.15' => 'upgrade_to_0_10_15',
        '0.10.24' => 'upgrade_to_0_10_24',
        '0.10.27' => 'upgrade_to_0_10_27',
        '0.13.6' => 'upgrade_to_0_13_6',
        '0.13.9' => 'upgrade_to_0_13_9',
        '0.14.0' => 'upgrade_to_0_14_0',
        '0.14.2' => 'upgrade_to_0_14_2',
    ];
    protected $progressBar;
    protected $signature = 'dwfw:upgrade
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';
    protected $description = 'Upgrade Different Web FrameWork requirements on dev, publish files.';

    public function handle()
    {
        $this->makeConfigFileIfNotExists();
        $current_version = config('dwfw.version') !== '0.13.10' && config('dwfw.version') !== '0.13.11' ? config('dwfw.version') : '0.13.9';
        foreach ($this->upgrade_methods as $version => $upgrade) {
            if ($version > $current_version ?? '0.10.13') {
                if (is_callable([$this, $upgrade])) {
                    $this->$upgrade();
                } else {
                    $this->error(' Error while upgrading, missing upgrade method');
                    exit;
                }
            }
        }
        $this->updateVersionNumber();
        $this->finish_message = ' DWFW succesfully upgraded.';
        $this->info($this->finish_message);
    }

    private function start_progress_bar($version, int $max = 5, int $min_sec_between_redraws = 0, int $max_sec_between_redraws = 120, int $redraw_frequency = 1)
    {
        $this->progressBar = $this->output->createProgressBar($max);
        $this->progressBar->minSecondsBetweenRedraws($min_sec_between_redraws);
        $this->progressBar->maxSecondsBetweenRedraws($max_sec_between_redraws);
        $this->progressBar->setRedrawFrequency($redraw_frequency);

        $this->progressBar->start();

        $this->info("\nUpgrading to " . $version . '. Please wait...');
        $this->progressBar->advance();
    }

    private function makeConfigFileIfNotExists()
    {
        if (!File::exists(config_path('dwfw.php'))) {
            $this->line(' Publishing dwfw config file (dwfw.php)');
            File::copy(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config/dwfw.php', config_path('dwfw.php'));
        }
    }

    private function updateVersionNumber()
    {
        $disk = Storage::disk(config('backpack.base.root_disk_name'));
        $contents = $disk->get('config/dwfw.php');
        $this->line(' Publishing new version number');
        $disk->put('config/dwfw.php', str_replace(config('dwfw.version'), self::VERSION, $contents));
        return config('dwfw.version');
    }

    private function insertStringToFile($file_path, $string = '', $is_config_entry = false)
    {
        $disk = Storage::disk(config('backpack.base.root_disk_name'));

        if ($disk->exists($file_path)) {
            $contents = $disk->get($file_path);
            $file_lines = file($disk->path($file_path), FILE_IGNORE_NEW_LINES);

            if ($this->getLastLineNumberThatContains($string, $file_lines)) {
                return $this->info(' String already exists in ' . $file_path . '.');
            }
            if (!$is_config_entry && $disk->put($file_path, $contents . PHP_EOL . $string)) {
                $this->info(' Successfully added string to ' . $file_path . '.');
            } else {
                if ($is_config_entry && $disk->put($file_path, str_replace(']', "\t" . $string . ",\n]", $contents))) {
                    $this->info(' Successfully added config entry to ' . $file_path . '.');
                } else {
                    $this->error(' Could not write to ' . $file_path . ' file.');
                }
            }
        } else {
            $this->error('The ' . $file_path . ' file does not exist.');
        }
    }

    /**
     * Parse the given file stream and return the line number where a string is found.
     *
     * @param string $needle The string that's being searched for.
     * @param array $haystack The file where the search is being performed.
     * @return bool|int         The last line number where the string was found. Or false.
     */
    private function getLastLineNumberThatContains($needle, $haystack)
    {
        $matchingLines = array_filter($haystack, function ($k) use ($needle) {
            return strpos($k, $needle) !== false;
        });

        if ($matchingLines) {
            return array_key_last($matchingLines);
        }

        return false;
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

        $this->progressBar->finish();
    }

    private function upgrade_to_0_10_15()
    {
        $this->start_progress_bar('0.10.15', 3);
        $this->line(' Updating Backpack translations');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.langs',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_10_24()
    {
        $this->start_progress_bar('0.10.24', 3);
        $this->line(' Publishing test utilities');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'tests.utilities',
        ]);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_10_27()
    {
        $this->start_progress_bar('0.10.27', 2);
        $this->insertStringToFile('config/dwfw.php', "'profile_has_image' => true", true);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_13_6()
    {
        $this->start_progress_bar('0.13.6', 1);
        $this->line(' Publishing backpack login view');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.login',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_13_9()
    {
        $this->start_progress_bar('0.13.9', 6);
        $this->line(' Publishing backpack login view');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.login',
            '--force' => '--force',
        ]);
        $this->line(' Publishing spatie honey response class');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'spatie-honey.spam-respond',
            '--force' => '--force',
        ]);
        $this->line(' Publishing spatie honey config file');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Spatie\Honeypot\HoneypotServiceProvider',
            '--tag' => 'config',
            '--force' => '--force',
        ]);
        $this->line(' Publishing ip check config file');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'config.checkIp',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_14_0()
    {
        $this->start_progress_bar('0.14.0', 4);
        $this->line(' Publishing cache config file');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'config.cache',
            '--force' => '--force',
        ]);
        $this->line(' Publishing index.php file');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'index',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }

    private function upgrade_to_0_14_2()
    {
        $this->start_progress_bar('0.14.2', 2);
        $this->line(' Publishing select2 ajax multiple filter');
        $this->executeArtisanProcess('vendor:publish', [
            '--provider' => 'Different\Dwfw\DwfwServiceProvider',
            '--tag' => 'backpack.filters',
            '--force' => '--force',
        ]);
        $this->progressBar->finish();
    }
}
