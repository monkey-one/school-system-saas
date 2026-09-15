@extends('portal.layout')

@section('title', $messages->first()->subject ?? __('Messages'))

@section('content')
<a href="{{ route($portal . '.messages') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-navy-500 hover:underline">&larr; {{ __('All conversations') }}</a>

<section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
    @if ($messages->first()->student)
        <p class="mb-4 text-xs text-gray-500">{{ __('About') }}: {{ $messages->first()->student->full_name }}</p>
    @endif
    <div class="space-y-4">
        @foreach ($messages as $message)
            @php $mine = $message->sender_id === auth()->id(); @endphp
            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-4 py-3 {{ $mine ? 'rounded-br-sm bg-navy-600 text-white' : 'rounded-bl-sm bg-gray-100 text-gray-800' }}">
                    <p class="text-xs font-semibold {{ $mine ? 'text-white/70' : 'text-gray-500' }}">{{ $mine ? __('You') : $message->sender?->name }} · {{ $message->created_at->translatedFormat('d M Y H:i') }}</p>
                    <p class="mt-1 whitespace-pre-line break-words text-sm">{{ $message->content }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route($portal . '.messages.reply', $thread) }}" class="mt-6 space-y-3 border-t border-gray-100 pt-4">
        @csrf
        <label for="content" class="sr-only">{{ __('Reply') }}</label>
        <textarea id="content" name="content" rows="3" maxlength="5000" required placeholder="{{ __('Write a reply...') }}" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
        <div class="text-right">
            <button class="rounded-xl bg-navy-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ __('Send Reply') }}</button>
        </div>
    </form>
</section>
@endsection
