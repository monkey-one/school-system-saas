@extends('portal.layout')

@section('title', __('My Children'))

@section('content')
<section class="rounded-2xl bg-gradient-to-r from-navy-700 to-navy-500 p-6 text-white shadow-sm">
    <p class="text-sm text-white/70">{{ now()->translatedFormat('l, d F Y') }}</p>
    <h2 class="mt-1 font-heading text-2xl font-bold">{{ __('Welcome, :name', ['name' => auth()->user()->name]) }}</h2>
    <p class="mt-2 text-sm text-white/80">{{ __('Follow the attendance, grades, report cards and bills of your children in one place.') }}</p>
</section>

<div class="grid gap-4 md:grid-cols-2">
    @foreach ($cards as $card)
        @php $child = $card['student']; @endphp
        <a href="{{ route('parent.child', $child) }}" class="block rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition hover:border-navy-100 hover:shadow">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gold-100 font-heading text-lg font-bold text-navy-700">{{ mb_strtoupper(mb_substr($child->full_name, 0, 1)) }}</div>
                <div class="min-w-0">
                    <p class="truncate font-heading text-lg font-bold text-navy-700">{{ $child->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ __('Class') }} {{ $child->classroom?->name ?? '-' }} · NIS {{ $child->nis }}</p>
                </div>
            </div>
            <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[11px] font-medium uppercase text-gray-500">{{ __('Attendance') }}</p>
                    <p class="font-heading text-lg font-bold text-navy-700">{{ $card['attendance']['rate'] !== null ? $card['attendance']['rate'] . '%' : '—' }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[11px] font-medium uppercase text-gray-500">{{ __('Outstanding') }}</p>
                    <p class="font-heading text-sm font-bold {{ $card['bills']['outstanding'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ \App\Helpers\CurrencyHelper::format($card['bills']['outstanding']) }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-[11px] font-medium uppercase text-gray-500">{{ __('Report Cards') }}</p>
                    <p class="font-heading text-lg font-bold text-navy-700">{{ $card['reportCards'] }}</p>
                </div>
            </div>
        </a>
    @endforeach
</div>

<section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="font-heading font-bold text-navy-700">{{ __('Announcements') }}</h3>
        <a href="{{ route('parent.announcements') }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('See all') }}</a>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($announcements as $announcement)
            <div class="rounded-xl border border-gray-100 p-4">
                <p class="text-sm font-semibold text-gray-800">@if ($announcement->is_pinned)📌 @endif{{ $announcement->title }}</p>
                <p class="text-xs text-gray-500">{{ $announcement->published_at?->translatedFormat('d M Y') }}</p>
                <p class="mt-1 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 140) }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">{{ __('No announcements.') }}</p>
        @endforelse
    </div>
</section>
@endsection
