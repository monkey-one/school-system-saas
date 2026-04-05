<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status PPDB</title>

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
                        gold: { 400: '#FBBF24', 500: '#F59E0B' },
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
<body class="bg-gray-50 min-h-screen font-body">

    <div x-data="statusChecker()" class="min-h-screen flex flex-col">
        {{-- Header --}}
        <header class="bg-navy-600 text-white py-6 shadow-lg">
            <div class="max-w-2xl mx-auto px-4 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-2xl font-heading font-bold">Edu<span class="text-gold-400">SaaS</span></span>
                </div>
                <h1 class="text-xl font-heading font-bold">Cek Status Pendaftaran PPDB</h1>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                {{-- Search Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-navy-600/10 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-gray-500 text-sm">Masukkan nomor pendaftaran untuk mengecek status PPDB Anda.</p>
                    </div>

                    <form @submit.prevent="checkStatus()">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Pendaftaran</label>
                            <input type="text" x-model="regNumber" required placeholder="Contoh: PPDB-2026-00001"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none text-center text-lg font-semibold tracking-wider uppercase">
                        </div>
                        <button type="submit" :disabled="loading"
                                class="w-full py-3.5 rounded-xl font-bold text-white bg-navy-600 hover:bg-navy-700 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Cek Status
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Mencari...
                                </span>
                            </template>
                        </button>
                    </form>
                </div>

                {{-- Result --}}
                <template x-if="result">
                    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" x-transition>
                        <h3 class="text-lg font-heading font-bold text-navy-600 mb-4">Hasil Pencarian</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">No. Pendaftaran</span>
                                <span class="font-semibold text-navy-600" x-text="result.registration_number"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Nama</span>
                                <span class="font-medium text-gray-800" x-text="result.full_name"></span>
                            </div>
                            <hr class="border-gray-100">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Status</span>
                                <span class="px-4 py-1.5 rounded-full text-sm font-bold"
                                      :class="{
                                          'bg-yellow-100 text-yellow-700': result.status === 'pending',
                                          'bg-blue-100 text-blue-700': result.status === 'reviewing',
                                          'bg-green-100 text-green-700': result.status === 'accepted',
                                          'bg-red-100 text-red-700': result.status === 'rejected',
                                      }"
                                      x-text="statusLabels[result.status] || result.status"></span>
                            </div>
                            <template x-if="result.notes">
                                <div class="bg-gray-50 rounded-xl p-4 mt-2">
                                    <p class="text-sm text-gray-500 font-medium mb-1">Catatan:</p>
                                    <p class="text-sm text-gray-700" x-text="result.notes"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Error --}}
                <template x-if="error">
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-2xl p-6 text-center" x-transition>
                        <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-red-600 font-medium" x-text="error"></p>
                    </div>
                </template>

                <div class="text-center mt-6">
                    <a href="/ppdb/register" class="text-navy-600 hover:text-navy-700 font-medium text-sm inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Kembali ke halaman pendaftaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function statusChecker() {
            return {
                regNumber: '',
                loading: false,
                result: null,
                error: null,
                statusLabels: {
                    'pending': 'Menunggu Review',
                    'reviewing': 'Sedang Direview',
                    'accepted': 'Diterima',
                    'rejected': 'Ditolak',
                },

                async checkStatus() {
                    this.result = null;
                    this.error = null;
                    this.loading = true;

                    try {
                        const response = await fetch(`/api/ppdb/status/${encodeURIComponent(this.regNumber)}`, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (response.ok) {
                            this.result = await response.json();
                        } else if (response.status === 404) {
                            this.error = 'Nomor pendaftaran tidak ditemukan. Pastikan nomor yang Anda masukkan benar.';
                        } else {
                            this.error = 'Terjadi kesalahan. Silakan coba lagi.';
                        }
                    } catch (e) {
                        this.error = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
