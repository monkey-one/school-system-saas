@extends('website.layout')

@section('title', __('Teachers & Staff'))

@section('content')
@include('website.partials.page-header', ['title' => __('Teachers & Staff'), 'subtitle' => __(':count educators and education staff', ['count' => $teachers->count()])])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6" x-data="{ q: '' }">
    <input type="search" x-model="q" placeholder="{{ __('Search by name or subject...') }}" class="w-full rounded-full border-gray-300 px-5 text-sm sm:w-80">

    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach ($teachers as $teacher)
            <div x-show="q === '' || @js(mb_strtolower($teacher->full_name . ' ' . $teacher->major . ' ' . $teacher->position)).includes(q.toLowerCase())">
                @include('website.partials.teacher-card', ['teacher' => $teacher])
            </div>
        @endforeach
    </div>
</section>
@endsection
