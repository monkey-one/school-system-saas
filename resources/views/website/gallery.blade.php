@extends('website.layout')

@section('title', __('Gallery'))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => __('Gallery'), 'subtitle' => __('Photo and video documentation of school activities')])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse ($albums as $album)
            @include('website.partials.album-card', ['album' => $album, 'tq' => $tq])
        @empty
            <p class="text-gray-500">{{ __('No albums yet.') }}</p>
        @endforelse
    </div>
    <div class="mt-10">{{ $albums->links() }}</div>
</section>
@endsection
