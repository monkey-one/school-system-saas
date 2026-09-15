{{-- Floating call-to-action shown only on the public demo (DEMO_MODE=true).
     Inline styles so it renders the same in Filament panels, portals and
     public pages regardless of their CSS. --}}
@if (\App\Support\Demo::enabled() && filled(config('demo.author.url')))
    @php
        $author = config('demo.author');
        $wa = preg_replace('/\D/', '', (string) ($author['whatsapp'] ?? ''));
    @endphp
    <div x-data="{ open: true }" style="position:fixed;right:16px;bottom:16px;z-index:60;font-family:Inter,ui-sans-serif,system-ui,sans-serif;max-width:calc(100vw - 32px)">
        <div x-show="open" style="display:flex;align-items:center;gap:10px;background:#0f2440;color:#fff;border-radius:14px;padding:10px 12px 10px 14px;box-shadow:0 10px 30px rgba(0,0,0,.25);font-size:13px;line-height:1.3">
            <div style="min-width:0">
                <div style="font-weight:700">{{ __('Live demo by :brand', ['brand' => $author['brand']]) }}</div>
                <div style="opacity:.75;font-size:12px">{{ __('Want this system for your school?') }}</div>
            </div>
            <a href="{{ $author['url'] }}" target="_blank" rel="noopener" style="background:#f59e0b;color:#0f2440;font-weight:700;border-radius:10px;padding:8px 12px;text-decoration:none;white-space:nowrap">{{ $author['brand'] }}</a>
            @if ($wa)
                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" aria-label="WhatsApp" style="background:#22c55e;color:#fff;border-radius:10px;padding:8px;display:flex;text-decoration:none">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.8 11.8 0 0012.04 0C5.46 0 .1 5.35.1 11.93c0 2.1.55 4.16 1.6 5.97L0 24l6.26-1.64a11.9 11.9 0 005.78 1.47h.01c6.58 0 11.94-5.35 11.94-11.93 0-3.19-1.24-6.18-3.47-8.42zM12.05 21.8h-.01a9.9 9.9 0 01-5.04-1.38l-.36-.21-3.72.97.99-3.62-.23-.37a9.86 9.86 0 01-1.52-5.26c0-5.47 4.46-9.92 9.93-9.92a9.9 9.9 0 019.92 9.93c0 5.47-4.45 9.86-9.96 9.86zm5.44-7.4c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.7.63.71.23 1.36.2 1.87.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35z"/></svg>
                </a>
            @endif
            <button type="button" x-on:click="open = false" aria-label="{{ __('Close') }}" style="background:transparent;border:0;color:#fff;opacity:.6;cursor:pointer;font-size:18px;line-height:1;padding:0 2px">&times;</button>
        </div>
    </div>
@endif
