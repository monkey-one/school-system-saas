@extends('student-portal.layout')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')

@section('content')
<div class="space-y-4">
    @forelse($announcements ?? [] as $announcement)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    @if($announcement->is_important)
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Penting</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $announcement->created_at->diffForHumans() }}</span>
                </div>
                <h3 class="text-base font-heading font-bold text-navy-600 mb-2">{{ $announcement->title }}</h3>
                <div class="text-sm text-gray-600 leading-relaxed">
                    {!! Str::limit(strip_tags($announcement->content), 200) !!}
                </div>
            </div>
            @if($announcement->attachment)
            <div class="flex-shrink-0">
                <a href="{{ Storage::url($announcement->attachment) }}" target="_blank"
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-navy-50 text-navy-600 text-xs font-medium rounded-lg hover:bg-navy-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Lampiran
                </a>
            </div>
            @endif
        </div>

        @if(strlen(strip_tags($announcement->content)) > 200)
        <div x-data="{ expanded: false }" class="mt-3">
            <button @click="expanded = !expanded" class="text-sm text-gold-600 hover:text-gold-700 font-medium">
                <span x-text="expanded ? 'Sembunyikan' : 'Baca selengkapnya'"></span>
            </button>
            <div x-show="expanded" x-collapse class="mt-3 text-sm text-gray-600 leading-relaxed">
                {!! nl2br(e($announcement->content)) !!}
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        <p class="text-gray-400">Belum ada pengumuman.</p>
    </div>
    @endforelse

    @if(isset($announcements) && $announcements instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>
    @endif
</div>
@endsection
