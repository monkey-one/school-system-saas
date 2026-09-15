@extends('portal.layout')

@section('title', $assignment->title)

@section('content')
<a href="{{ route($portal . '.assignments', $childParam) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-navy-500 hover:underline">&larr; {{ __('All assignments') }}</a>

<div class="grid gap-6 lg:grid-cols-5">
    <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-3">
        <p class="text-xs font-semibold uppercase text-gold-600">{{ $assignment->classroomSubject?->subject?->name }} · {{ $assignment->teacher?->full_name }}</p>
        <h2 class="mt-1 font-heading text-2xl font-bold text-navy-700">{{ $assignment->title }}</h2>
        <p class="mt-2 text-sm {{ $assignment->due_at->isPast() ? 'font-semibold text-red-600' : 'text-gray-500' }}">
            {{ __('Due') }} {{ $assignment->due_at->translatedFormat('l, d F Y H:i') }} · {{ __('Maximum score') }} {{ $assignment->max_score }}
        </p>
        <div class="prose prose-sm mt-5 max-w-none text-gray-700">{{ \App\Support\SafeHtml::basic($assignment->instructions) }}</div>
        @if ($assignment->attachment)
            <a href="{{ route('elearning.assignments.attachment', $assignment) }}" target="_blank" class="mt-5 inline-flex items-center gap-2 rounded-xl border border-navy-600 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50">{{ __('Download material') }}</a>
        @endif
    </article>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
        <h3 class="font-heading font-bold text-navy-700">{{ __('Submission') }}</h3>

        @if ($submission)
            <div class="mt-3 space-y-2 text-sm">
                <p class="text-gray-500">{{ __('Submitted') }} {{ $submission->submitted_at->translatedFormat('d M Y H:i') }} @if ($submission->is_late)· <span class="font-semibold text-amber-600">{{ __('late') }}</span>@endif</p>
                @if ($submission->content)<p class="whitespace-pre-line rounded-xl bg-slate-50 p-3 text-gray-700">{{ $submission->content }}</p>@endif
                @if ($submission->attachment)<a href="{{ route('elearning.submissions.attachment', $submission) }}" target="_blank" class="font-semibold text-navy-500 hover:underline">{{ __('Open attached file') }}</a>@endif
            </div>
            @if ($submission->score !== null)
                <div class="mt-4 rounded-xl bg-green-50 p-4">
                    <p class="text-sm text-green-800">{{ __('Score') }}</p>
                    <p class="font-heading text-3xl font-bold text-green-700">{{ rtrim(rtrim((string) $submission->score, '0'), '.') }} <span class="text-base text-green-600">/ {{ $assignment->max_score }}</span></p>
                    @if ($submission->feedback)<p class="mt-2 whitespace-pre-line text-sm text-green-900">{{ $submission->feedback }}</p>@endif
                </div>
            @endif
        @endif

        @if ($portal === 'student' && $assignment->acceptsSubmissions() && $submission?->score === null)
            <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf
                <textarea name="content" rows="6" maxlength="10000" placeholder="{{ __('Write your answer...') }}" class="w-full rounded-lg border-gray-300 text-sm">{{ old('content', $submission?->content) }}</textarea>
                <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-navy-50 file:px-3 file:py-2 file:font-semibold file:text-navy-700">
                <p class="text-xs text-gray-500">{{ __('Text and/or a file (max. 10 MB). You can resubmit until the teacher grades it.') }}</p>
                <button class="w-full rounded-xl bg-navy-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ $submission ? __('Resubmit') : __('Submit work') }}</button>
            </form>
        @elseif (! $submission)
            <p class="mt-3 text-sm text-gray-500">{{ $portal === 'parent' ? __('Not submitted yet.') : __('Submissions for this assignment are closed.') }}</p>
        @endif
    </section>
</div>
@endsection
