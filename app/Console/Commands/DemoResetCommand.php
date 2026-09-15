<?php

namespace App\Console\Commands;

use App\Support\Demo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

// Restores the public demo to its freshly seeded state: rebuilds the whole
// database and removes files uploaded by visitors. Refuses to run unless
// DEMO_MODE is enabled, so it can never wipe a real school's data by accident.
class DemoResetCommand extends Command
{
    protected $signature = 'edusaas:demo-reset {--force : Run even when DEMO_MODE is disabled}';

    protected $description = 'Reset the public demo database and uploaded files (destroys ALL data)';

    public function handle(): int
    {
        if (! Demo::enabled() && ! $this->option('force')) {
            $this->error('DEMO_MODE is disabled. Refusing to wipe data (use --force to override).');

            return self::FAILURE;
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $disk = Storage::disk('public');

        foreach ($disk->directories() as $directory) {
            $disk->deleteDirectory($directory);
        }

        foreach ($disk->files() as $file) {
            if (! str_starts_with(basename($file), '.')) {
                $disk->delete($file);
            }
        }

        $this->info('Demo data restored.');

        return self::SUCCESS;
    }
}
