@php $author = config('demo.author'); @endphp
@if (filled($author['name'] ?? null))
    <span class="whitespace-nowrap">· {{ __('Developed by') }}
        <a href="{{ $author['url'] }}" target="_blank" rel="noopener" class="font-semibold hover:underline">{{ $author['name'] }}</a>
    </span>
@endif
