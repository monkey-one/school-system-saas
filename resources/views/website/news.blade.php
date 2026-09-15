@extends('website.layout')

@section('title', __('News & Articles'))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => __('News & Articles'), 'subtitle' => __('Activities, announcements and stories from :school', ['school' => $tenant->name])])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        @if ($tq)<input type="hidden" name="tenant" value="{{ $tenant->slug }}">@endif
        <a href="{{ route('website.news', $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ! $category ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ __('All') }}</a>
        @foreach (\App\Models\Post::categoryLabels() as $key => $label)
            <a href="{{ route('website.news', ['category' => $key] + $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $category === $key ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ $label }}</a>
        @endforeach
        <input type="search" name="q" value="{{ request('q') }}" maxlength="100" placeholder="{{ __('Search news...') }}" class="ml-auto w-full rounded-full border-gray-300 px-4 text-sm sm:w-64">
    </form>

    <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($posts as $post)
            @include('website.partials.post-card', ['post' => $post, 'tq' => $tq])
        @empty
            <p class="text-gray-500">{{ __('No news found.') }}</p>
        @endforelse
    </div>

    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
