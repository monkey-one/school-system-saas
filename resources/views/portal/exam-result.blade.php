@extends('portal.layout')

@section('title', __('Exam result'))

@section('content')
<section class="mx-auto max-w-lg rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-sm">
    <p class="text-xs font-semibold uppercase text-gold-600">{{ $exam->classroomSubject?->subject?->name }}</p>
    <h2 class="mt-1 font-heading text-2xl font-bold text-navy-700">{{ $exam->title }}</h2>
    <p class="mt-1 text-sm text-gray-500">{{ __('Submitted') }} {{ $attempt->submitted_at->translatedFormat('d M Y H:i') }}</p>

    @if ($exam->show_result)
        <p class="mt-6 font-heading text-6xl font-extrabold {{ (float) $attempt->score >= 75 ? 'text-green-600' : 'text-amber-600' }}">{{ rtrim(rtrim((string) $attempt->score, '0'), '.') }}</p>
        <p class="mt-2 text-gray-600">{{ __(':correct of :total answers correct', ['correct' => $attempt->correct_count, 'total' => $total]) }}</p>
    @else
        <p class="mt-6 text-gray-600">{{ __('Submitted. The score will be announced by the teacher.') }}</p>
    @endif

    <a href="{{ route('student.exams') }}" class="mt-8 inline-block rounded-xl bg-navy-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ __('Back to exams') }}</a>
</section>
@endsection
