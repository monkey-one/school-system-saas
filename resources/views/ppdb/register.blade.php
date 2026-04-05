<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Peserta Didik Baru (PPDB)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 600: '#1E3A5F', 700: '#172D4A', 800: '#102035' },
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
<body class="bg-gray-50 min-h-screen font-body">

    <div x-data="ppdbForm()" class="min-h-screen">
        {{-- Header --}}
        <header class="bg-navy-600 text-white py-6 shadow-lg">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-2xl font-heading font-bold">Edu<span class="text-gold-400">SaaS</span></span>
                </div>
                <h1 class="text-xl font-heading font-bold">Pendaftaran Peserta Didik Baru (PPDB)</h1>
                <p class="text-white/60 text-sm mt-1">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
            </div>
        </header>

        {{-- Progress Bar --}}
        <div class="max-w-3xl mx-auto px-4 pt-8">
            <div class="flex items-center justify-between mb-2">
                <template x-for="(label, index) in stepLabels" :key="index">
                    <div class="flex items-center" :class="index < stepLabels.length - 1 ? 'flex-1' : ''">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                                 :class="currentStep > index + 1 ? 'bg-green-500 text-white' : (currentStep === index + 1 ? 'bg-navy-600 text-white ring-4 ring-navy-600/20' : 'bg-gray-200 text-gray-500')">
                                <template x-if="currentStep > index + 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="currentStep <= index + 1">
                                    <span x-text="index + 1"></span>
                                </template>
                            </div>
                            <span class="text-xs mt-2 font-medium hidden sm:block" :class="currentStep >= index + 1 ? 'text-navy-600' : 'text-gray-400'" x-text="label"></span>
                        </div>
                        <div x-show="index < stepLabels.length - 1" class="flex-1 h-1 mx-3 rounded-full transition-all duration-300"
                             :class="currentStep > index + 1 ? 'bg-green-500' : 'bg-gray-200'"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Form --}}
        <div class="max-w-3xl mx-auto px-4 py-8">
            <template x-if="!submitted">
                <form @submit.prevent="submitForm">

                    {{-- Step 1: Data Calon Siswa --}}
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-heading font-bold text-navy-600 mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Data Calon Siswa
                            </h2>

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.full_name" required placeholder="Masukkan nama lengkap sesuai akta"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                    <template x-if="errors.full_name"><p class="text-red-500 text-xs mt-1" x-text="errors.full_name"></p></template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="form.birth_date" required
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                    <template x-if="errors.birth_date"><p class="text-red-500 text-xs mt-1" x-text="errors.birth_date"></p></template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <select x-model="form.gender" required
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                    <template x-if="errors.gender"><p class="text-red-500 text-xs mt-1" x-text="errors.gender"></p></template>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah</label>
                                    <input type="text" x-model="form.previous_school" placeholder="Nama sekolah sebelumnya"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                    <textarea x-model="form.address" required rows="3" placeholder="Masukkan alamat lengkap"
                                              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none resize-none"></textarea>
                                    <template x-if="errors.address"><p class="text-red-500 text-xs mt-1" x-text="errors.address"></p></template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Data Orang Tua --}}
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-heading font-bold text-navy-600 mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Data Orang Tua / Wali
                            </h2>

                            <div class="grid sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Orang Tua / Wali <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.parent_name" required placeholder="Nama lengkap orang tua/wali"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                    <template x-if="errors.parent_name"><p class="text-red-500 text-xs mt-1" x-text="errors.parent_name"></p></template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon / WhatsApp <span class="text-red-500">*</span></label>
                                    <input type="tel" x-model="form.parent_phone" required placeholder="08xxxxxxxxxx"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                    <template x-if="errors.parent_phone"><p class="text-red-500 text-xs mt-1" x-text="errors.parent_phone"></p></template>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                                    <input type="email" x-model="form.parent_email" placeholder="email@contoh.com"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 transition-all outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Upload Dokumen --}}
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-heading font-bold text-navy-600 mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload Dokumen
                            </h2>

                            <p class="text-gray-500 text-sm mb-6">Upload dokumen yang diperlukan dalam format JPG, PNG, atau PDF (maks. 2MB per file).</p>

                            @php
                                $documents = [
                                    ['key' => 'akta_lahir', 'label' => 'Akta Kelahiran', 'required' => true],
                                    ['key' => 'kartu_keluarga', 'label' => 'Kartu Keluarga', 'required' => true],
                                    ['key' => 'foto', 'label' => 'Pas Foto 3x4', 'required' => true],
                                    ['key' => 'ijazah', 'label' => 'Ijazah / Surat Keterangan Lulus', 'required' => false],
                                ];
                            @endphp

                            <div class="space-y-4">
                                @foreach($documents as $doc)
                                <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 hover:border-navy-600/30 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-700">
                                            {{ $doc['label'] }}
                                            @if($doc['required']) <span class="text-red-500">*</span> @endif
                                        </label>
                                        <span class="text-xs text-gray-400">Maks. 2MB</span>
                                    </div>
                                    <input type="file" name="documents[{{ $doc['key'] }}]" accept=".jpg,.jpeg,.png,.pdf"
                                           {{ $doc['required'] ? '' : '' }}
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-navy-600/10 file:text-navy-600 hover:file:bg-navy-600/20 cursor-pointer">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Review & Submit --}}
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                            <h2 class="text-xl font-heading font-bold text-navy-600 mb-6 flex items-center gap-2">
                                <svg class="w-6 h-6 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Review Data Pendaftaran
                            </h2>

                            <div class="space-y-6">
                                {{-- Student data summary --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Data Calon Siswa</h3>
                                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Nama Lengkap</span><span class="text-navy-600 font-medium text-sm" x-text="form.full_name"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Tanggal Lahir</span><span class="text-navy-600 font-medium text-sm" x-text="form.birth_date"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Jenis Kelamin</span><span class="text-navy-600 font-medium text-sm" x-text="form.gender === 'male' ? 'Laki-laki' : 'Perempuan'"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Asal Sekolah</span><span class="text-navy-600 font-medium text-sm" x-text="form.previous_school || '-'"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Alamat</span><span class="text-navy-600 font-medium text-sm text-right max-w-xs" x-text="form.address"></span></div>
                                    </div>
                                </div>

                                {{-- Parent data summary --}}
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Data Orang Tua / Wali</h3>
                                    <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Nama</span><span class="text-navy-600 font-medium text-sm" x-text="form.parent_name"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Telepon</span><span class="text-navy-600 font-medium text-sm" x-text="form.parent_phone"></span></div>
                                        <div class="flex justify-between"><span class="text-gray-500 text-sm">Email</span><span class="text-navy-600 font-medium text-sm" x-text="form.parent_email || '-'"></span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-gold-500/10 border border-gold-500/20 rounded-xl">
                                <p class="text-sm text-gold-700"><strong>Perhatian:</strong> Pastikan semua data yang Anda masukkan sudah benar. Data yang sudah dikirim tidak dapat diubah.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="flex justify-between mt-6">
                        <button type="button" x-show="currentStep > 1" @click="prevStep()"
                                class="px-6 py-3 rounded-xl font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Sebelumnya
                        </button>
                        <div x-show="currentStep === 1"></div>

                        <button type="button" x-show="currentStep < 4" @click="nextStep()"
                                class="px-6 py-3 rounded-xl font-semibold text-white bg-navy-600 hover:bg-navy-700 transition-all flex items-center gap-2 ml-auto">
                            Selanjutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <button type="submit" x-show="currentStep === 4" :disabled="submitting"
                                class="px-8 py-3 rounded-xl font-bold text-white bg-green-600 hover:bg-green-700 transition-all flex items-center gap-2 ml-auto disabled:opacity-50">
                            <template x-if="!submitting">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Kirim Pendaftaran
                                </span>
                            </template>
                            <template x-if="submitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                    Mengirim...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </template>

            {{-- Success Page --}}
            <template x-if="submitted">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center" x-transition>
                    <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-2xl font-heading font-bold text-navy-600 mb-2">Pendaftaran Berhasil!</h2>
                    <p class="text-gray-500 mb-8">Terima kasih telah mendaftarkan calon peserta didik baru.</p>

                    <div class="bg-navy-600 text-white rounded-2xl p-8 mb-8 max-w-sm mx-auto">
                        <p class="text-white/60 text-sm mb-2">Nomor Pendaftaran Anda</p>
                        <p class="text-3xl font-heading font-extrabold tracking-wider" x-text="registrationNumber"></p>
                    </div>

                    <div class="bg-gold-500/10 border border-gold-500/20 rounded-xl p-4 mb-8 max-w-md mx-auto">
                        <p class="text-sm text-gold-700"><strong>Penting!</strong> Simpan nomor pendaftaran ini. Anda dapat menggunakan nomor ini untuk mengecek status pendaftaran.</p>
                    </div>

                    <a href="/ppdb/status" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-navy-600 bg-navy-600/10 hover:bg-navy-600/20 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cek Status Pendaftaran
                    </a>
                </div>
            </template>
        </div>
    </div>

    <script>
        function ppdbForm() {
            return {
                currentStep: 1,
                submitted: false,
                submitting: false,
                registrationNumber: '',
                stepLabels: ['Data Siswa', 'Data Orang Tua', 'Dokumen', 'Review'],
                form: {
                    full_name: '',
                    birth_date: '',
                    gender: '',
                    previous_school: '',
                    address: '',
                    parent_name: '',
                    parent_phone: '',
                    parent_email: '',
                },
                errors: {},

                validateStep(step) {
                    this.errors = {};
                    if (step === 1) {
                        if (!this.form.full_name.trim()) this.errors.full_name = 'Nama lengkap wajib diisi';
                        if (!this.form.birth_date) this.errors.birth_date = 'Tanggal lahir wajib diisi';
                        if (!this.form.gender) this.errors.gender = 'Jenis kelamin wajib dipilih';
                        if (!this.form.address.trim()) this.errors.address = 'Alamat wajib diisi';
                    }
                    if (step === 2) {
                        if (!this.form.parent_name.trim()) this.errors.parent_name = 'Nama orang tua wajib diisi';
                        if (!this.form.parent_phone.trim()) this.errors.parent_phone = 'Nomor telepon wajib diisi';
                    }
                    return Object.keys(this.errors).length === 0;
                },

                nextStep() {
                    if (this.validateStep(this.currentStep)) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                prevStep() {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async submitForm() {
                    this.submitting = true;

                    const formData = new FormData();
                    for (const [key, value] of Object.entries(this.form)) {
                        formData.append(key, value);
                    }

                    const fileInputs = document.querySelectorAll('input[type="file"]');
                    fileInputs.forEach(input => {
                        if (input.files[0]) {
                            formData.append(input.name, input.files[0]);
                        }
                    });

                    try {
                        const response = await fetch('/api/ppdb/register', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.registrationNumber = data.registration_number;
                            this.submitted = true;
                        } else {
                            alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                    } finally {
                        this.submitting = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
