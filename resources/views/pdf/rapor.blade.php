<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Siswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.5;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #1E3A5F;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 70px;
            height: 70px;
        }
        .header-logo .logo-placeholder {
            width: 70px;
            height: 70px;
            border: 2px solid #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header-text h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #1E3A5F;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text .address {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }
        .header-text .npsn {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }
        .report-title {
            text-align: center;
            margin: 20px 0;
        }
        .report-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1E3A5F;
            border-bottom: 2px solid #1E3A5F;
            display: inline-block;
            padding-bottom: 4px;
        }
        .student-info {
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 2px 0;
            font-size: 11pt;
        }
        .student-info .label {
            width: 140px;
            font-weight: bold;
        }
        .student-info .separator {
            width: 15px;
            text-align: center;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #555;
            padding: 6px 10px;
            text-align: left;
            font-size: 10pt;
        }
        .grades-table th {
            background-color: #1E3A5F;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .grades-table td.center {
            text-align: center;
        }
        .grades-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .grades-table .no-col {
            width: 35px;
            text-align: center;
        }
        .attendance-table {
            width: 50%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .attendance-table th, .attendance-table td {
            border: 1px solid #555;
            padding: 5px 10px;
            font-size: 10pt;
        }
        .attendance-table th {
            background-color: #1E3A5F;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .attendance-table td.center {
            text-align: center;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1E3A5F;
            margin: 15px 0 8px;
            text-transform: uppercase;
        }
        .extracurricular-table {
            width: 70%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .extracurricular-table th, .extracurricular-table td {
            border: 1px solid #555;
            padding: 5px 10px;
            font-size: 10pt;
        }
        .extracurricular-table th {
            background-color: #1E3A5F;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .comments {
            margin-bottom: 15px;
        }
        .comments .comment-box {
            border: 1px solid #ccc;
            padding: 10px 12px;
            min-height: 50px;
            font-size: 10pt;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .comments .comment-label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 4px;
        }
        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature-block {
            display: table-cell;
            text-align: center;
            width: 50%;
            font-size: 10pt;
        }
        .signature-block .sig-line {
            border-bottom: 1px solid #333;
            width: 180px;
            margin: 60px auto 5px;
        }
        .signature-block .sig-name {
            font-weight: bold;
        }
        .signature-block .sig-nip {
            font-size: 9pt;
            color: #666;
        }
        .date-location {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- School Header --}}
        <div class="header">
            <div class="header-content">
                <div class="header-logo">
                    @if(!empty($school->logo))
                        <img src="{{ public_path('storage/' . $school->logo) }}" alt="Logo">
                    @else
                        <div class="logo-placeholder">LOGO</div>
                    @endif
                </div>
                <div class="header-text">
                    <h1>{{ $school->name ?? 'NAMA SEKOLAH' }}</h1>
                    <div class="address">{{ $school->address ?? 'Alamat Sekolah' }}</div>
                    <div class="npsn">NPSN: {{ $school->npsn ?? '-' }} | Telp: {{ $school->phone ?? '-' }} | Email: {{ $school->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Report Title --}}
        <div class="report-title">
            <h2>Laporan Hasil Belajar Peserta Didik</h2>
        </div>

        {{-- Student Info --}}
        <div class="student-info">
            <table>
                <tr>
                    <td class="label">Nama Peserta Didik</td>
                    <td class="separator">:</td>
                    <td>{{ $student->full_name ?? '-' }}</td>
                    <td class="label" style="padding-left: 20px;">Kelas</td>
                    <td class="separator">:</td>
                    <td>{{ $classroom->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIS</td>
                    <td class="separator">:</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td class="label" style="padding-left: 20px;">Semester</td>
                    <td class="separator">:</td>
                    <td>{{ $semester ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NISN</td>
                    <td class="separator">:</td>
                    <td>{{ $student->nisn ?? '-' }}</td>
                    <td class="label" style="padding-left: 20px;">Tahun Pelajaran</td>
                    <td class="separator">:</td>
                    <td>{{ $academicYear->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Grades Table --}}
        <div class="section-title">A. Nilai Akademik</div>
        <table class="grades-table">
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width: 60px;">Nilai</th>
                    <th style="width: 70px;">Predikat</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grades ?? [] as $index => $grade)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $grade['subject_name'] ?? '-' }}</td>
                    <td class="center">{{ $grade['score'] ?? '-' }}</td>
                    <td class="center">{{ $grade['predicate'] ?? '-' }}</td>
                    <td>{{ $grade['description'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="center" style="padding: 15px; color: #999;">Belum ada data nilai</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Attendance Summary --}}
        <div class="section-title">B. Kehadiran</div>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Alfa</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">{{ $attendance['hadir'] ?? 0 }} hari</td>
                    <td class="center">{{ $attendance['sakit'] ?? 0 }} hari</td>
                    <td class="center">{{ $attendance['izin'] ?? 0 }} hari</td>
                    <td class="center">{{ $attendance['alfa'] ?? 0 }} hari</td>
                </tr>
            </tbody>
        </table>

        {{-- Extracurricular --}}
        <div class="section-title">C. Ekstrakurikuler</div>
        <table class="extracurricular-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Kegiatan Ekstrakurikuler</th>
                    <th style="width: 80px;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($extracurriculars ?? [] as $index => $ekskul)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $ekskul['name'] ?? '-' }}</td>
                    <td class="center">{{ $ekskul['predicate'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="center" style="padding: 10px; color: #999;">-</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Comments --}}
        <div class="section-title">D. Catatan</div>
        <div class="comments">
            <div class="comment-label">Catatan Wali Kelas:</div>
            <div class="comment-box">{{ $homeroomComment ?? '' }}</div>

            <div class="comment-label">Catatan Kepala Sekolah:</div>
            <div class="comment-box">{{ $principalComment ?? '' }}</div>
        </div>

        {{-- Signatures --}}
        <div class="date-location">
            {{ $school->city ?? '.........' }}, {{ $reportDate ?? now()->translatedFormat('d F Y') }}
        </div>

        <div class="signatures">
            <div class="signature-block">
                <div>Wali Kelas</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $homeroomTeacher->full_name ?? '........................' }}</div>
                <div class="sig-nip">NIP. {{ $homeroomTeacher->nip ?? '........................' }}</div>
            </div>
            <div class="signature-block">
                <div>Kepala Sekolah</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $school->principal_name ?? '........................' }}</div>
                <div class="sig-nip">NIP. {{ $principalNip ?? '........................' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
