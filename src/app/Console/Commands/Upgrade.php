<?php

namespace Different\Dwfw\app\Console\Commands;

use Backpack\CRUD\app\Console\Commands\Traits\PrettyCommandOutput;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Upgrade extends Command
{

    use PrettyCommandOutput;

    const VERSION = '0.10.36';
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
    ];
    protected $progressBar;
    protected $signature = 'dwfw:upgrade
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';
    protected $description = 'Upgrade Different Web FrameWork requirements on dev, publish files.';

    public function handle()
    {
        $this->makeConfigFileIfNotExists();
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

}
