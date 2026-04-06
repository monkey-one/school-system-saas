<div class="flex items-center gap-1">
    @php $currentLocale = app()->getLocale(); @endphp
    <a href="{{ route('locale.switch', 'id') }}"
       wire:navigate.hover="false"
       x-on:click.prevent="window.location.href = '{{ route('locale.switch', 'id') }}'"
       class="inline-flex items-center justify-center rounded-lg px-2 py-1 text-xs font-medium transition {{ $currentLocale === 'id' ? 'bg-primary-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}"
       title="Indonesia">
        ID
    </a>
    <a href="{{ route('locale.switch', 'en') }}"
       wire:navigate.hover="false"
       x-on:click.prevent="window.location.href = '{{ route('locale.switch', 'en') }}'"
       class="inline-flex items-center justify-center rounded-lg px-2 py-1 text-xs font-medium transition {{ $currentLocale === 'en' ? 'bg-primary-500 text-white' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800' }}"
       title="English">
        EN
    </a>
</div>
