<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran {{ $registration->registration_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #222; }
        .header { border-bottom: 3px double #1E3A5F; padding-bottom: 10px; text-align: center; }
        .header h1 { margin: 0; font-size: 16pt; color: #1E3A5F; }
        .header p { margin: 2px 0; font-size: 9pt; }
        h2 { text-align: center; font-size: 13pt; margin: 18px 0 4px; text-transform: uppercase; }
        .number { text-align: center; font-size: 18pt; font-weight: bold; letter-spacing: 1px; margin: 8px 0 18px; border: 2px dashed #F59E0B; padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 4px; vertical-align: top; }
        td.label { width: 38%; color: #555; }
        .docs td { border: 1px solid #ccc; }
        .note { margin-top: 18px; font-size: 9pt; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>{{ $tenant->address }}, {{ $tenant->city }} · Telp {{ $tenant->phone }} · {{ $tenant->email }}</p>
    </div>

    <h2>Bukti Pendaftaran Peserta Didik Baru</h2>
    <p style="text-align:center;margin:0">{{ $registration->ppdbWave?->name }} · Tahun Ajaran {{ $registration->ppdbWave?->academicYear?->name }}</p>
    <div class="number">{{ $registration->registration_number }}</div>

    <table>
        <tr><td class="label">Nama Lengkap</td><td>: {{ $registration->full_name }}</td></tr>
        <tr><td class="label">Tanggal Lahir</td><td>: {{ $registration->birth_date?->translatedFormat('d F Y') }}</td></tr>
        <tr><td class="label">Jenis Kelamin</td><td>: {{ $registration->gender?->label() }}</td></tr>
        <tr><td class="label">Sekolah Asal</td><td>: {{ $registration->previous_school ?? '-' }}</td></tr>
        <tr><td class="label">Alamat</td><td>: {{ $registration->address }}</td></tr>
        <tr><td class="label">Nama Orang Tua/Wali</td><td>: {{ $registration->parent_name }}</td></tr>
        <tr><td class="label">No. Telepon/WhatsApp</td><td>: {{ $registration->parent_phone }}</td></tr>
        <tr><td class="label">Tanggal Daftar</td><td>: {{ $registration->created_at->translatedFormat('d F Y H:i') }}</td></tr>
    </table>

    <p style="margin-top:16px;font-weight:bold">Dokumen yang diunggah</p>
    <table class="docs">
        @foreach ($documents as $key => [$label])
            <tr><td>{{ $label }}</td><td style="width:25%;text-align:center">{{ ! empty($registration->documents[$key]) ? 'Ada' : '-' }}</td></tr>
        @endforeach
    </table>

    <p class="note">Simpan bukti ini. Cek hasil seleksi di halaman status PPDB menggunakan nomor pendaftaran dan tanggal lahir calon siswa.</p>
</body>
</html>
