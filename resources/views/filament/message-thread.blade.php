<div class="space-y-3">
    @if ($messages->first()?->student)
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('About') }}: {{ $messages->first()->student->full_name }}</p>
    @endif

    @foreach ($messages as $message)
        @php $mine = $message->sender_id === auth()->id(); @endphp
        <div @class(['flex', 'justify-end' => $mine, 'justify-start' => ! $mine])>
            <div @class([
                'max-w-[85%] rounded-xl px-4 py-3',
                'bg-primary-600 text-white' => $mine,
                'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100' => ! $mine,
            ])>
                <p @class(['text-xs font-semibold', 'text-white/70' => $mine, 'text-gray-500 dark:text-gray-400' => ! $mine])>
                    {{ $mine ? __('You') : $message->sender?->name }} · {{ $message->created_at->translatedFormat('d M Y H:i') }}
                </p>
                <p class="mt-1 whitespace-pre-line break-words text-sm">{{ $message->content }}</p>
            </div>
        </div>
    @endforeach
</div>
