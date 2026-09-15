<div class="space-y-4 text-center">
    <div>
        <p class="text-base font-semibold text-gray-900 dark:text-white">
            {{ $session->classroomSubject?->subject?->name }} · {{ $session->classroomSubject?->classroom?->name }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $session->date?->translatedFormat('l, d F Y') }}
            @if ($session->start_time) · {{ substr((string) $session->start_time, 0, 5) }}–{{ substr((string) $session->end_time, 0, 5) }} @endif
        </p>
    </div>

    <img src="{{ $qr['qr_image'] }}" alt="QR" class="mx-auto w-72 max-w-full rounded-xl border border-gray-200 bg-white p-2 dark:border-gray-700">

    <p class="text-sm text-gray-600 dark:text-gray-300">
        {{ __('Students scan this code with their phone camera and sign in to check in.') }}
    </p>
    <p class="text-xs text-gray-500 dark:text-gray-400">
        {{ __('Valid until :time. Reopen this dialog to generate a new code.', ['time' => $qr['expires_at']->format('H:i')]) }}
        · {{ __('Checked in so far: :count', ['count' => $session->student_attendances_count]) }}
    </p>

    <div x-data="{ copied: false }" class="flex items-center gap-2">
        <input type="text" readonly value="{{ $qr['url'] }}" class="block w-full truncate rounded-lg border-gray-300 text-xs dark:border-gray-600 dark:bg-gray-800">
        <button type="button"
                x-on:click="navigator.clipboard.writeText(@js($qr['url'])); copied = true; setTimeout(() => copied = false, 2000)"
                class="shrink-0 rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white hover:bg-primary-500">
            <span x-show="! copied">{{ __('Copy link') }}</span>
            <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
        </button>
    </div>
</div>
