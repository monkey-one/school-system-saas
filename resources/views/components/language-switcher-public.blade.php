<div class="relative" x-data="{ langOpen: false }">
    <button @click="langOpen = !langOpen" class="flex items-center gap-1.5 text-white/80 hover:text-white transition-colors text-sm font-medium px-3 py-1.5 rounded-lg hover:bg-white/10">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="langOpen" @click.away="langOpen = false" x-transition class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
        <a href="{{ route('locale.switch', 'id') }}" class="block px-4 py-2.5 text-sm font-medium {{ app()->getLocale() === 'id' ? 'bg-navy-50 text-navy-600' : 'text-gray-600 hover:bg-gray-50' }}">🇮🇩 Indonesia</a>
        <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2.5 text-sm font-medium {{ app()->getLocale() === 'en' ? 'bg-navy-50 text-navy-600' : 'text-gray-600 hover:bg-gray-50' }}">🇬🇧 English</a>
    </div>
</div>
