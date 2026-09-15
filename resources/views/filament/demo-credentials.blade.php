{{-- Demo accounts on the login page (DEMO_MODE only). Clicking a row fills
     the form; the visitor still presses "Sign in". Inline styles because the
     Filament theme only ships the utility classes its own views use. --}}
@if (\App\Support\Demo::enabled())
    <div style="margin-top:1.5rem;border:1px solid rgba(245,158,11,.4);background:rgba(245,158,11,.08);border-radius:.75rem;padding:.75rem;font-size:.875rem">
        <p style="margin:0 0 .5rem;font-weight:600;color:#b45309">
            {{ __('Demo accounts') }}
            <span style="font-weight:400">· {{ __('password') }}: <code>{{ config('demo.password') }}</code></span>
        </p>
        @foreach (config('demo.accounts') as $role => $email)
            <button type="button"
                    x-on:click="$wire.set('data.email', @js($email)); $wire.set('data.password', @js(config('demo.password')))"
                    style="display:flex;width:100%;align-items:center;justify-content:space-between;gap:.5rem;border:0;background:transparent;border-radius:.5rem;padding:.375rem .5rem;text-align:left;cursor:pointer;color:inherit"
                    onmouseover="this.style.background='rgba(245,158,11,.15)'" onmouseout="this.style.background='transparent'">
                <span style="font-weight:500">{{ __($role) }}</span>
                <span style="font-size:.75rem;opacity:.7;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $email }}</span>
            </button>
        @endforeach
    </div>
@endif
