@extends('portal.layout')

@section('title', __('Messages'))

@section('content')
<div class="grid gap-6 lg:grid-cols-5">
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
        <h2 class="font-heading font-bold text-navy-700">{{ __('New message') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('Send a message to the homeroom or subject teacher of :name.', ['name' => $student->full_name]) }}</p>

        @if ($recipients->isEmpty())
            <p class="mt-4 text-sm text-gray-500">{{ __('No teachers are available to message yet.') }}</p>
        @else
            <form method="POST" action="{{ route($portal . '.messages.send') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="recipient_id" class="mb-1 block text-sm font-medium text-gray-700">{{ __('To') }}</label>
                    <select id="recipient_id" name="recipient_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach ($recipients as $recipient)
                            <option value="{{ $recipient['user_id'] }}" @selected(old('recipient_id') == $recipient['user_id'])>{{ $recipient['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Subject') }}</label>
                    <input id="subject" name="subject" type="text" maxlength="150" required value="{{ old('subject') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="content" class="mb-1 block text-sm font-medium text-gray-700">{{ __('Message') }}</label>
                    <textarea id="content" name="content" rows="5" maxlength="5000" required class="w-full rounded-lg border-gray-300 text-sm">{{ old('content') }}</textarea>
                </div>
                <button class="w-full rounded-xl bg-navy-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ __('Send Message') }}</button>
            </form>
        @endif
    </section>

    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm lg:col-span-3">
        <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Conversations') }}</h2>
        <div class="divide-y divide-gray-100">
            @forelse ($threads as $message)
                @php $other = $message->sender_id === auth()->id() ? $message->recipient : $message->sender; @endphp
                <a href="{{ route($portal . '.messages.thread', $message->thread_id) }}" class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-navy-100 font-bold text-navy-700">{{ mb_strtoupper(mb_substr($other?->name ?? '?', 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-3">
                            <p class="truncate font-semibold text-gray-800">{{ $other?->name ?? __('Unknown user') }}</p>
                            <span class="shrink-0 text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="truncate text-sm font-medium text-gray-700">{{ $message->subject ?? __('(no subject)') }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $message->content }}</p>
                    </div>
                    @if (($unreadByThread[$message->thread_id] ?? 0) > 0)
                        <span class="rounded-full bg-gold-500 px-2 py-0.5 text-xs font-bold text-navy-800">{{ $unreadByThread[$message->thread_id] }}</span>
                    @endif
                </a>
            @empty
                <p class="px-6 py-10 text-center text-sm text-gray-500">{{ __('No conversations yet.') }}</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
