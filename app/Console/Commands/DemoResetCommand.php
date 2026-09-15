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

    // Private upload folders on the local disk (PPDB documents, leave letters,
    // e-learning files, Filament import/export files).
    private const PRIVATE_DIRECTORIES = ['ppdb', 'leave-requests', 'assignments', 'submissions', 'students', 'filament_exports', 'filament_imports', 'livewire-tmp'];

    public function handle(): int
    {
        if (! Demo::enabled() && ! $this->option('force')) {
            $this->error('DEMO_MODE is disabled. Refusing to wipe data (use --force to override).');

            return self::FAILURE;
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $public = Storage::disk('public');

        foreach ($public->directories() as $directory) {
            $public->deleteDirectory($directory);
        }

        foreach ($public->files() as $file) {
            if (! str_starts_with(basename($file), '.')) {
                $public->delete($file);
            }
        }

        foreach (self::PRIVATE_DIRECTORIES as $directory) {
            Storage::disk('local')->deleteDirectory($directory);
        }

        // Jobs queued before the reset refer to records that no longer exist.
        $this->callSilently('queue:clear', ['--force' => true]);
        $this->callSilently('queue:restart');
        $this->callSilently('cache:clear');

        $this->info('Demo data restored.');

        return self::SUCCESS;
    }
}
