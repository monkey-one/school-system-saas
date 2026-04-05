# EduSaaS — UI/UX Design Specification

## Design Philosophy

Premium, trustworthy, professional. Target users are school administrators in Indonesia who are used to government/education software that is ugly. EduSaaS should feel like a breath of fresh air — clean, fast, modern.

## Color Palette

```css
:root {
  /* Primary */
  --primary-900: #0F2A47;
  --primary-800: #1E3A5F;   /* Main Navy - primary brand */
  --primary-700: #2D4E7A;
  --primary-600: #3B6494;
  --primary-500: #4A7AAF;
  --primary-400: #6B96C4;
  --primary-100: #E8F0FB;

  /* Accent */
  --accent-600: #D97706;
  --accent-500: #F59E0B;    /* Gold accent */
  --accent-400: #FBBF24;
  --accent-100: #FEF3C7;

  /* Semantic */
  --success: #10B981;
  --warning: #F59E0B;
  --danger: #EF4444;
  --info: #3B82F6;

  /* Neutral */
  --gray-900: #111827;
  --gray-800: #1F2937;
  --gray-700: #374151;
  --gray-500: #6B7280;
  --gray-300: #D1D5DB;
  --gray-100: #F3F4F6;
  --white: #FFFFFF;
}
```

## Typography

```css
/* Headers - Authoritative, Professional */
font-family: 'Plus Jakarta Sans', sans-serif;
/* CDN: https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800 */

/* Body - Readable, Clean */
font-family: 'Inter', sans-serif;

/* Monospace - Code, IDs */
font-family: 'JetBrains Mono', monospace;

/* Scale */
--text-xs: 0.75rem;    /* 12px - badges, labels */
--text-sm: 0.875rem;   /* 14px - table cells, captions */
--text-base: 1rem;     /* 16px - body */
--text-lg: 1.125rem;   /* 18px - section headers */
--text-xl: 1.25rem;    /* 20px - card titles */
--text-2xl: 1.5rem;    /* 24px - page titles */
--text-3xl: 1.875rem;  /* 30px - dashboard headlines */
--text-4xl: 2.25rem;   /* 36px - landing page headers */
```

## Filament Custom Theme

Override Filament defaults in `resources/css/filament/school-admin/theme.css`:

```css
/* Panel background */
.fi-body {
  background-color: var(--gray-100);
}

/* Sidebar */
.fi-sidebar {
  background: linear-gradient(180deg, var(--primary-900) 0%, var(--primary-800) 100%);
}

.fi-sidebar-item-label {
  color: rgba(255,255,255,0.85);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 500;
}

.fi-sidebar-item-active .fi-sidebar-item-label {
  color: var(--accent-400);
}

/* Top nav */
.fi-topbar {
  background: white;
  border-bottom: 1px solid var(--gray-200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

/* Cards */
.fi-card {
  border-radius: 12px;
  border: 1px solid var(--gray-200);
  box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03);
}

/* Primary button */
.fi-btn-primary {
  background: var(--primary-800);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 600;
  border-radius: 8px;
}

.fi-btn-primary:hover {
  background: var(--primary-700);
}

/* Tables */
.fi-table-header-cell {
  background: var(--gray-50);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-weight: 600;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--gray-500);
}
```

## Dashboard Widgets

### School Admin Dashboard Layout:

```
┌─────────────────────────────────────────────────────────┐
│  Selamat pagi, Bapak Ahmad 👋    Senin, 05 April 2026   │
│  SMP Negeri 1 Jakarta                                    │
├──────────┬──────────┬──────────┬────────────────────────┤
│ 📚 Siswa │ 👨‍🏫 Guru  │ 💰 SPP   │ 📋 Kehadiran Hari Ini  │
│   847    │    45    │ Rp 12jt  │    742 / 847 (87%)     │
│  aktif   │  aktif   │ bulan ini│  [progress bar]        │
├──────────┴──────────┴──────────┼────────────────────────┤
│  Grafik Penerimaan SPP 6 Bulan │  Agenda Hari Ini       │
│  [ApexCharts Bar Chart]        │  ─────────────────     │
│                                │  07:30 Upacara         │
│                                │  08:00 Belajar Kls 7A  │
│                                │  10:00 Rapat Guru      │
├────────────────────────────────┼────────────────────────┤
│  Siswa Belum Bayar SPP (Top 5) │  Pengumuman Terbaru    │
│  [Table: nama, kelas, tunggak] │  [List with badges]    │
├────────────────────────────────┼────────────────────────┤
│  Kehadiran Per Kelas (Heatmap) │  PPDB Statistik        │
│  [ApexCharts Heatmap]          │  Daftar: 45            │
│                                │  Diterima: 32          │
│                                │  Menunggu: 8           │
└────────────────────────────────┴────────────────────────┘
```

## Status Badge Colors

```php
// Use in Filament Tables
->badge()
->color(fn (string $state): string => match ($state) {
    'active', 'hadir', 'paid', 'accepted', 'good' => 'success',
    'pending', 'partial', 'waitlist', 'minor_damage' => 'warning',
    'inactive', 'alfa', 'unpaid', 'rejected', 'major_damage' => 'danger',
    'transferred', 'overdue', 'sakit' => 'gray',
    'izin', 'trial' => 'info',
    default => 'gray',
})
```

## Form Design Patterns

```php
// Section grouping
Forms\Components\Section::make('Data Pribadi')
    ->description('Informasi dasar siswa')
    ->icon('heroicon-o-user')
    ->collapsible()
    ->schema([...])

// Two-column grid for forms
Forms\Components\Grid::make(2)->schema([...])

// Photo upload with preview
Forms\Components\FileUpload::make('photo')
    ->image()
    ->imageEditor()
    ->imageEditorAspectRatios(['1:1'])
    ->avatar()
    ->disk('public')
    ->directory('students')
    ->maxSize(2048)
    ->label('Foto Siswa')
```

## Table Design Patterns

```php
// Every table must have:
Tables\Columns\TextColumn::make('created_at')
    ->dateTime('d M Y')
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true)

// With search, filter, and export
->searchable(['full_name', 'nis', 'nisn'])
->filters([...])
->headerActions([
    ExportAction::make()->exporter(StudentExporter::class)
])
->bulkActions([
    Tables\Actions\BulkActionGroup::make([
        Tables\Actions\DeleteBulkAction::make(),
        // custom bulk actions
    ])
])
->striped()
->defaultSort('full_name')
->paginationPageOptions([25, 50, 100])
```

## Key Page Designs

### PPDB Public Form:
- Clean, single-page form with steps (multi-step wizard)
- Step 1: Data Calon Siswa
- Step 2: Data Orang Tua
- Step 3: Upload Dokumen
- Step 4: Review & Submit
- Progress bar at top
- Mobile-optimized (many parents use phone)
- WhatsApp number field with WA icon
- After submit: show nomor pendaftaran prominently

### Rapor PDF Design:
- A4 portrait
- School header with logo, name, address, NPSN
- Student photo (right side)
- Grade table: No | Mata Pelajaran | Nilai | Predikat | Deskripsi
- Footer: attendance summary, extracurricular
- Signature section: homeroom teacher + principal
- School stamp placeholder
- Font: Times New Roman (formal, official Indonesian document feel)

### SPP Bill / Receipt:
- Clean invoice layout
- School logo + name header
- Bill details: student name, NIS, kelas, periode
- Itemized: SPP Pokok, diskon (if any)
- Total amount in big font
- Payment status badge (LUNAS / BELUM BAYAR)
- QR Code for online payment
- Footer: thank you message

## Mobile Responsiveness

All pages must work on mobile. Priority pages for mobile optimization:
1. PPDB public form (parents register via phone)
2. Student portal (students check grades/schedule)
3. Parent portal (parents monitor)
4. Attendance QR scan page
5. SPP payment page

Use Filament's built-in responsive behavior + custom Tailwind breakpoints.

## Loading States

Every async action must have a loading state:
- Filament tables: built-in skeleton loading
- Form submissions: disable button + spinner
- Dashboard stats: number counter animation on load
- Charts: loading skeleton → reveal with fade-in

## Empty States

Custom empty state for all tables:
```php
->emptyStateIcon('heroicon-o-...')
->emptyStateHeading('Belum ada data')
->emptyStateDescription('Klik tombol "Tambah" untuk menambahkan data pertama.')
->emptyStateActions([
    Tables\Actions\CreateAction::make()
])
```
