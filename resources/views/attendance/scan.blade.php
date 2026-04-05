<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Kehadiran</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 600: '#1E3A5F', 700: '#172D4A' },
                        gold: { 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706' },
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
        h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-body flex items-center justify-center p-4" x-data="attendanceScan()">

    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xl font-heading font-bold text-navy-600">Edu<span class="text-gold-500">SaaS</span></span>
            </div>
        </div>

        {{-- Initial / Loading State --}}
        <template x-if="state === 'initial'">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Session Info --}}
                <div class="bg-navy-600 text-white p-6">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <h2 class="text-lg font-heading font-bold">Konfirmasi Kehadiran</h2>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Mata Pelajaran</div>
                                <div class="font-semibold text-gray-800 text-sm" x-text="sessionInfo.subject_name">-</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Kelas</div>
                                <div class="font-semibold text-gray-800 text-sm" x-text="sessionInfo.classroom_name">-</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Tanggal & Waktu</div>
                                <div class="font-semibold text-gray-800 text-sm" x-text="sessionInfo.date">-</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400">Guru</div>
                                <div class="font-semibold text-gray-800 text-sm" x-text="sessionInfo.teacher_name">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Late warning --}}
                    <template x-if="isLate">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-2 text-yellow-700">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                <span class="text-sm font-semibold">Anda terlambat!</span>
                            </div>
                            <p class="text-xs text-yellow-600 mt-1">Kehadiran Anda akan dicatat sebagai <strong>Terlambat</strong>.</p>
                        </div>
                    </template>

                    <button @click="confirmAttendance()" :disabled="loading"
                            class="w-full py-4 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all disabled:opacity-50 flex items-center justify-center gap-2 text-lg">
                        <template x-if="!loading">
                            <span class="flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Konfirmasi Kehadiran
                            </span>
                        </template>
                        <template x-if="loading">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Memproses...
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </template>

        {{-- Success State --}}
        <template x-if="state === 'success'">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center" x-transition>
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-heading font-bold text-gray-800 mb-2">Kehadiran Tercatat!</h2>
                <p class="text-gray-500 text-sm mb-4">Kehadiran Anda berhasil dikonfirmasi.</p>

                <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-400">Status</span>
                        <span class="font-semibold" :class="isLate ? 'text-yellow-600' : 'text-green-600'" x-text="isLate ? 'Terlambat' : 'Hadir'"></span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-400">Waktu</span><span class="text-gray-800 font-medium" x-text="checkInTime"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Mata Pelajaran</span><span class="text-gray-800 font-medium" x-text="sessionInfo.subject_name"></span></div>
                </div>

                <p class="text-xs text-gray-400 mt-6">Anda dapat menutup halaman ini.</p>
            </div>
        </template>

        {{-- Error State --}}
        <template x-if="state === 'error'">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center" x-transition>
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h2 class="text-xl font-heading font-bold text-gray-800 mb-2">Gagal!</h2>
                <p class="text-gray-500 text-sm mb-6" x-text="errorMessage"></p>
                <button @click="state = 'initial'" class="px-6 py-3 rounded-xl font-semibold text-navy-600 bg-navy-600/10 hover:bg-navy-600/20 transition-all">
                    Coba Lagi
                </button>
            </div>
        </template>
    </div>

    <script>
        function attendanceScan() {
            return {
                state: 'initial',
                loading: false,
                isLate: false,
                checkInTime: '',
                errorMessage: '',
                sessionInfo: {
                    subject_name: '{{ $session->classroomSubject->subject->name ?? "Mata Pelajaran" }}',
                    classroom_name: '{{ $session->classroomSubject->classroom->name ?? "Kelas" }}',
                    date: '{{ isset($session) ? $session->date->translatedFormat("l, d F Y") . " | " . $session->start_time . " - " . $session->end_time : "-" }}',
                    teacher_name: '{{ $session->teacher->full_name ?? "Guru" }}',
                },

                init() {
                    // Auto-detect late attendance
                    @if(isset($session) && isset($session->start_time))
                    const now = new Date();
                    const [h, m] = '{{ $session->start_time }}'.split(':');
                    const startTime = new Date();
                    startTime.setHours(parseInt(h), parseInt(m), 0);
                    // Consider late if more than 15 minutes after start
                    this.isLate = now > new Date(startTime.getTime() + 15 * 60000);
                    @endif
                },

                async confirmAttendance() {
                    this.loading = true;

                    try {
                        const response = await fetch('{{ route("attendance.confirm", ["token" => $session->qr_token ?? ""]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.checkInTime = data.check_in_time || new Date().toLocaleTimeString('id-ID');
                            this.isLate = data.is_late || this.isLate;
                            this.state = 'success';
                        } else {
                            this.errorMessage = data.message || 'Terjadi kesalahan saat mengkonfirmasi kehadiran.';
                            this.state = 'error';
                        }
                    } catch (error) {
                        this.errorMessage = 'Terjadi kesalahan jaringan. Pastikan Anda terhubung ke internet.';
                        this.state = 'error';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
