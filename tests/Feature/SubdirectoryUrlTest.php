<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

// Installations served from a sub-directory (https://site.com/edusaas) must
// keep that prefix in the Livewire script and update URLs, otherwise the
// browser posts to the main domain and every Filament panel stops working.
class SubdirectoryUrlTest extends TestCase
{
    protected function tearDown(): void
    {
        Request::setTrustedProxies([], 0);

        parent::tearDown();
    }

    public function test_livewire_urls_keep_the_subdirectory_prefix(): void
    {
        config(['app.url' => 'https://example.test/edusaas']);

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('https://example.test/edusaas/livewire/livewire.min.js', config('livewire.asset_url'));
        $this->assertSame('/edusaas/livewire/update', Livewire::getUpdateUri());
    }

    // The live demo runs behind a proxy that strips the prefix from the path
    // and announces it with X-Forwarded-Prefix, which Laravel then treats as
    // the request base path.
    public function test_prefix_is_not_duplicated_behind_a_forwarding_proxy(): void
    {
        config(['app.url' => 'https://example.test/edusaas']);

        Request::setTrustedProxies(['127.0.0.1'], Request::HEADER_X_FORWARDED_PREFIX);
        $request = Request::create('https://example.test/edusaas-admin/login', server: ['REMOTE_ADDR' => '127.0.0.1']);
        $request->headers->set('X-Forwarded-Prefix', '/edusaas');
        $this->app->instance('request', $request);
        URL::setRequest($request);

        $this->assertSame('/edusaas', $request->getBaseUrl());

        (new AppServiceProvider($this->app))->boot();

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
