<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.5;
        }
        .receipt {
            width: 210mm;
            padding: 15mm 20mm;
            margin: 0 auto;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1E3A5F;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-logo {
            display: table-cell;
            width: 70px;
            vertical-align: middle;
        }
        .header-logo img {
            width: 60px;
            height: 60px;
        }
        .header-logo .logo-placeholder {
            width: 60px;
            height: 60px;
            border: 2px solid #ccc;
            border-radius: 8px;
            text-align: center;
            line-height: 60px;
            font-size: 8pt;
            color: #999;
        }
        .header-text {
            display: table-cell;
            vertical-align: middle;
        }
        .header-text h1 {
            font-size: 14pt;
            font-weight: bold;
            color: #1E3A5F;
        }
        .header-text .address {
            font-size: 9pt;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            margin: 15px 0 20px;
        }
        .receipt-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1E3A5F;
            letter-spacing: 2px;
        }
        .receipt-meta {
            margin-bottom: 20px;
        }
        .receipt-meta table {
            width: 100%;
        }
        .receipt-meta td {
            padding: 2px 0;
            font-size: 10pt;
        }
        .receipt-meta .label {
            width: 140px;
            font-weight: bold;
        }
        .receipt-meta .separator {
            width: 15px;
            text-align: center;
        }
        .student-section {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        .student-section .section-label {
            font-weight: bold;
            color: #1E3A5F;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 10pt;
        }
        .items-table th {
            background-color: #1E3A5F;
            color: white;
            font-weight: bold;
            text-align: left;
        }
        .items-table td.right {
            text-align: right;
        }
        .items-table td.center {
            text-align: center;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .total-section {
            text-align: right;
            margin-bottom: 20px;
        }
        .total-box {
            display: inline-block;
            background-color: #1E3A5F;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
        }
        .total-label {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .total-amount {
            font-size: 18pt;
            font-weight: bold;
        }
        .payment-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .payment-info-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .payment-info .info-row {
            font-size: 10pt;
            padding: 2px 0;
        }
        .payment-info .info-label {
            font-weight: bold;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            font-size: 9pt;
            color: #999;
        }
        .footer .thank-you {
            font-size: 11pt;
            color: #1E3A5F;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        {{-- Header --}}
        <div class="header">
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
                <div class="address">Telp: {{ $school->phone ?? '-' }} | Email: {{ $school->email ?? '-' }}</div>
            </div>
        </div>

        {{-- Receipt Title --}}
        <div class="receipt-title">
            <h2>Kwitansi Pembayaran</h2>
        </div>

        {{-- Receipt Meta --}}
        <div class="receipt-meta">
            <table>
                <tr>
                    <td class="label">No. Kwitansi</td>
                    <td class="separator">:</td>
                    <td>{{ $payment->reference_number ?? '-' }}</td>
                    <td class="label" style="padding-left: 20px;">Tanggal</td>
                    <td class="separator">:</td>
                    <td>{{ $payment->payment_date?->translatedFormat('d F Y') ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Student Info --}}
        <div class="student-section">
            <div class="section-label">Informasi Siswa</div>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100px; font-weight: bold;">Nama</td>
                    <td style="width: 10px;">:</td>
                    <td>{{ $student->full_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NIS</td>
                    <td>:</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Kelas</td>
                    <td>:</td>
                    <td>{{ $classroom->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- Payment Items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">No</th>
                    <th>Keterangan</th>
                    <th style="width: 80px; text-align: center;">Periode</th>
                    <th style="width: 130px; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items ?? [] as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['description'] ?? '-' }}</td>
                    <td class="center">{{ $item['period'] ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="center" style="padding: 15px; color: #999;">Tidak ada item pembayaran</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Total --}}
        <div class="total-section">
            <div class="total-box">
                <div class="total-label">Total Pembayaran</div>
                <div class="total-amount">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="payment-info">
            <div class="payment-info-cell">
                <div class="info-row">
                    <span class="info-label">Metode Pembayaran: </span>
                    {{ $payment->method?->label() ?? '-' }}
                </div>
            </div>
            <div class="payment-info-cell" style="text-align: right;">
                <div class="info-row">
                    <span class="info-label">Status: </span>
                    <span class="status-badge status-paid">Lunas</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="thank-you">Terima kasih atas pembayaran Anda.</div>
            <p>Kwitansi ini dicetak secara otomatis oleh sistem dan sah tanpa tanda tangan.</p>
            <p style="margin-top: 5px;">{{ $school->name ?? 'EduSaaS' }} &mdash; Sistem Manajemen Sekolah Modern</p>
        </div>
    </div>
</body>
</html>
