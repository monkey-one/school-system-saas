<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>{{ __('Attendance Confirmation') }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 600: '#1E3A5F', 700: '#172D4A' },
                        gold: { 100: '#FEF3C7', 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706' },
                    },
                    fontFamily: {
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-body flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-6">
        <span class="text-xl font-heading font-bold text-navy-600">Edu<span class="text-gold-500">SaaS</span></span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-navy-600 text-white p-6 text-center">
            <h1 class="text-lg font-heading font-bold">{{ __('Attendance Confirmation') }}</h1>
            @if ($student)
                <p class="text-sm text-white/70 mt-1">{{ $student->full_name }} · {{ $student->nis }}</p>
            @endif
        </div>

        <div class="p-6 space-y-4">
            @if ($session)
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('Subject') }}</dt><dd class="font-semibold text-gray-800 text-right">{{ $session->classroomSubject?->subject?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('Class') }}</dt><dd class="font-semibold text-gray-800 text-right">{{ $session->classroomSubject?->classroom?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('Date') }}</dt><dd class="font-semibold text-gray-800 text-right">{{ $session->date->translatedFormat('l, d F Y') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('Time') }}</dt><dd class="font-semibold text-gray-800 text-right">{{ substr((string) $session->start_time, 0, 5) }} – {{ substr((string) $session->end_time, 0, 5) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-400">{{ __('Teacher') }}</dt><dd class="font-semibold text-gray-800 text-right">{{ $session->teacher?->full_name ?? '-' }}</dd></div>
                </dl>
            @endif

            @if ($error)
                <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ $error }}</div>
            @elseif ($attendance)
                <div class="rounded-xl {{ $attendance->status->value === 'terlambat' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-green-50 border-green-200 text-green-800' }} border p-4 text-center">
                    <p class="font-heading font-bold text-base">
                        {{ $justRecorded ? __('Attendance recorded!') : __('You have already checked in for this session.') }}
                    </p>
                    <p class="text-sm mt-1">
                        {{ __('Status') }}: <strong>{{ $attendance->status->label() }}</strong>
                        · {{ $attendance->check_in_time?->format('H:i') }}
                    </p>
                </div>
                <a href="{{ route('student.dashboard') }}" class="block w-full text-center py-3 rounded-xl font-semibold text-navy-600 bg-navy-600/10 hover:bg-navy-600/20">{{ __('Go to Student Portal') }}</a>
            @else
                <form method="POST" action="{{ route('attendance.confirm') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="w-full py-4 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 text-lg">
                        {{ __('Confirm Attendance') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
</body>
</html>
