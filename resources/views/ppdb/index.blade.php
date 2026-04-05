<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Online - {{ $tenant->name ?? 'EduSaaS' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50: '#EEF2F7', 100: '#D4DEE9', 500: '#2D5F8A', 600: '#1E3A5F', 700: '#172D4A', 800: '#102035' },
                        gold: { 50: '#FFFBEB', 100: '#FEF3C7', 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706' },
                    },
                    fontFamily: {
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-body">

    {{-- Header --}}
    <header class="bg-navy-600 text-white shadow-lg">
        <div class="max-w-4xl mx-auto px-4 py-8 text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-2xl font-heading font-bold">Edu<span class="text-gold-400">SaaS</span></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-extrabold mb-2">Penerimaan Peserta Didik Baru (PPDB)</h1>
            <p class="text-white/60">{{ $tenant->name ?? '' }}</p>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-4xl mx-auto px-4 py-10">

        @if($waves->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-heading font-bold text-gray-800 mb-2">Belum Ada Gelombang PPDB</h2>
                <p class="text-gray-500 mb-6">Saat ini belum ada gelombang pendaftaran yang dibuka. Silakan cek kembali nanti.</p>
                <a href="{{ route('ppdb.status') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-navy-600 text-white font-semibold hover:bg-navy-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cek Status Pendaftaran
                </a>
            </div>
        @else
            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                @foreach($waves as $wave)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                    <div class="bg-gradient-to-r from-navy-600 to-navy-700 p-5">
                        <h3 class="text-lg font-heading font-bold text-white">{{ $wave->name }}</h3>
                        <p class="text-white/60 text-sm mt-1">{{ $wave->academicYear->name ?? '' }}</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-5 h-5 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-gray-600">{{ $wave->opens_at->translatedFormat('d F Y') }} — {{ $wave->closes_at->translatedFormat('d F Y') }}</span>
                            </div>
                            @if($wave->quota)
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-5 h-5 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-gray-600">Kuota: {{ $wave->quota }} siswa</span>
                            </div>
                            @endif
                            @if($wave->registration_fee)
                            <div class="flex items-center gap-3 text-sm">
                                <svg class="w-5 h-5 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-gray-600">Biaya: Rp {{ number_format($wave->registration_fee, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>
                        <a href="{{ route('ppdb.register', $wave) }}" class="block w-full text-center px-6 py-3 rounded-xl font-semibold text-white bg-gradient-to-r from-amber-400 to-amber-600 hover:opacity-90 transition-all shadow-lg shadow-amber-500/20">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('ppdb.status') }}" class="inline-flex items-center gap-2 text-navy-600 font-semibold hover:text-navy-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Sudah mendaftar? Cek status pendaftaran
                </a>
            </div>
        @endif

    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 py-6 mt-auto">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} EduSaaS — Sistem Manajemen Sekolah
        </div>
    </footer>

</body>
</html>
