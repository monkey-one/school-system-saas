<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penerimaan {{ $registration->registration_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #222; line-height: 1.55; }
        .header { border-bottom: 3px double #1E3A5F; padding-bottom: 10px; text-align: center; }
        .header h1 { margin: 0; font-size: 16pt; color: #1E3A5F; }
        .header p { margin: 2px 0; font-size: 9pt; }
        h2 { text-align: center; font-size: 13pt; margin: 20px 0 2px; text-decoration: underline; }
        .ref { text-align: center; font-size: 9pt; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0 10px 20px; }
        td { padding: 3px 4px; }
        td.label { width: 34%; }
        .sign { width: 45%; margin-left: 55%; margin-top: 30px; text-align: center; }
        .sign .space { height: 70px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <p>NPSN {{ $tenant->npsn ?? '-' }} · {{ $tenant->address }}, {{ $tenant->city }} · Telp {{ $tenant->phone }}</p>
    </div>

    <h2>SURAT KETERANGAN DITERIMA</h2>
    <p class="ref">Nomor: {{ $registration->registration_number }}/PPDB/{{ now()->year }}</p>

    <p>Kepala {{ $tenant->name }} dengan ini menerangkan bahwa calon peserta didik:</p>
    <table>
        <tr><td class="label">Nama Lengkap</td><td>: <strong>{{ $registration->full_name }}</strong></td></tr>
        <tr><td class="label">Tanggal Lahir</td><td>: {{ $registration->birth_date?->translatedFormat('d F Y') }}</td></tr>
        <tr><td class="label">Sekolah Asal</td><td>: {{ $registration->previous_school ?? '-' }}</td></tr>
        <tr><td class="label">Orang Tua/Wali</td><td>: {{ $registration->parent_name }}</td></tr>
        <tr><td class="label">Nomor Pendaftaran</td><td>: {{ $registration->registration_number }}</td></tr>
    </table>
    <p>dinyatakan <strong>DITERIMA</strong> sebagai peserta didik baru pada {{ $registration->ppdbWave?->name }}, Tahun Ajaran {{ $registration->ppdbWave?->academicYear?->name }}.</p>
    <p>Calon peserta didik diharapkan melakukan daftar ulang dengan membawa surat ini beserta dokumen asli sesuai jadwal yang diumumkan sekolah.</p>

    <div class="sign">
        <p>{{ $tenant->city }}, {{ ($registration->reviewed_at ?? now())->translatedFormat('d F Y') }}<br>Kepala Sekolah,</p>
        <div class="space"></div>
        <p><strong>{{ $tenant->principal_name ?? '........................' }}</strong><br>NIP. {{ $tenant->settings['principal_nip'] ?? '........................' }}</p>
    </div>
</body>
</html>
