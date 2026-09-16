<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Livewire\Livewire;
use Tests\TestCase;

// Installations served from a sub-directory (https://site.com/edusaas) must
// keep that prefix in the Livewire script and update URLs, otherwise the
// browser posts to the main domain and every Filament panel stops working.
class SubdirectoryUrlTest extends TestCase
{
    public function test_livewire_urls_keep_the_subdirectory_prefix(): void
    {
        config(['app.url' => 'https://example.test/edusaas']);

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('https://example.test/edusaas/livewire/livewire.min.js', config('livewire.asset_url'));
        $this->assertSame('/edusaas/livewire/update', Livewire::getUpdateUri());
    }

    public function test_root_installations_are_left_untouched(): void
    {
        config(['app.url' => 'https://example.test', 'livewire.asset_url' => null]);

        (new AppServiceProvider($this->app))->boot();

        $this->assertNull(config('livewire.asset_url'));
        $this->assertSame('/livewire/update', Livewire::getUpdateUri());
    }
}
