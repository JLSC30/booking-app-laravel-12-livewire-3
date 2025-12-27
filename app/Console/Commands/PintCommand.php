<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class PintCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pint
                            {paths?* : Paths or files to fix (optional)}
                            {--test : Run in test mode (no changes)}
                            {--dirty : Only fix uncommitted changes}
                            {--diff= : Fix only files differing from the given branch}
                            {--v : Verbose output}
                            {--preset= : Override the preset (e.g., laravel, psr12)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Laravel Pint to fix code style';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pintBinary = base_path('vendor/bin/pint');

        if (! file_exists($pintBinary)) {
            $this->error('Laravel Pint is not installed. Run: composer require --dev laravel/pint');

            return 1;
        }

        // Build the command arguments
        $args = [];

        if ($this->option('test')) {
            $args[] = '--test';
        }

        if ($this->option('dirty')) {
            $args[] = '--dirty';
        }

        if ($this->option('v')) {
            $args[] = '-v';
        }

        if ($preset = $this->option('preset')) {
            $args[] = '--preset='.$preset;
        }

        if ($diff = $this->option('diff')) {
            $args[] = '--diff='.$diff;
        }

        // Add any provided paths
        if ($paths = $this->argument('paths')) {
            $args = array_merge($args, $paths);
        }

        // Run Pint via Symfony Process
        $process = new Process([$pintBinary, ...$args]);
        $process->setTimeout(null); // No timeout for large projects
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return $process->getExitCode();
    }
}
