<?php

namespace App\Filament\Teacher\Pages;

use App\Enums\AttendanceStatus;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\Tenant;
use App\Support\Demo;
use App\Support\Geo;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

// Daily teacher check-in / check-out from the phone. The browser location is
// compared with the school coordinates (School Profile → Attendance settings)
// and must be within the configured radius. Late check-ins are marked
// "terlambat". The location check is skipped on the public demo.
class MyCheckIn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.teacher.pages.my-check-in';

    public static function getNavigationLabel(): string
    {
        return __('My Check-in');
    }

    public function getTitle(): string
    {
        return __('My Check-in');
    }

    public function getToday(): ?TeacherAttendance
    {
        return TeacherAttendance::where('teacher_id', $this->teacher()->id)->whereDate('date', today())->first();
    }

    public function getHistory(): Collection
    {
        return TeacherAttendance::where('teacher_id', $this->teacher()->id)->latest('date')->limit(14)->get();
    }

    public function settings(): array
    {
        $settings = Tenant::current()?->settings ?? [];

        return [
            'lat' => isset($settings['school_lat']) && $settings['school_lat'] !== '' ? (float) $settings['school_lat'] : null,
            'lng' => isset($settings['school_lng']) && $settings['school_lng'] !== '' ? (float) $settings['school_lng'] : null,
            'radius' => (int) ($settings['checkin_radius_m'] ?? 200),
            'start' => $settings['teacher_checkin_time'] ?? '07:00',
            'tolerance' => (int) ($settings['late_threshold_minutes'] ?? 15),
            'demo' => Demo::enabled(),
        ];
    }

    public function checkIn(?float $lat = null, ?float $lng = null): void
    {
        if ($this->getToday()) {
            Notification::make()->warning()->title(__('You have already checked in today.'))->send();

            return;
        }

        if (! $this->locationAllowed($lat, $lng)) {
            return;
        }

        $settings = $this->settings();
        $lateAfter = today()->setTimeFromTimeString($settings['start'])->addMinutes($settings['tolerance']);
        $late = now()->greaterThan($lateAfter);

        TeacherAttendance::create([
            'teacher_id' => $this->teacher()->id,
            'date' => today(),
            'check_in_time' => now(),
            'method' => 'gps',
            'location_lat' => $lat,
            'location_lng' => $lng,
            'status' => $late ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR,
        ]);

        Notification::make()
            ->success()
            ->title($late ? __('Checked in (late) at :time', ['time' => now()->format('H:i')]) : __('Checked in at :time', ['time' => now()->format('H:i')]))
            ->send();
    }

    public function checkOut(?float $lat = null, ?float $lng = null): void
    {
        $today = $this->getToday();

        if (! $today?->check_in_time || $today->check_out_time) {
            Notification::make()->warning()->title(__('Check in first, or you have already checked out.'))->send();

            return;
        }

        if (! $this->locationAllowed($lat, $lng)) {
            return;
        }

        $today->update(['check_out_time' => now()]);

        Notification::make()->success()->title(__('Checked out at :time', ['time' => now()->format('H:i')]))->send();
    }

    private function locationAllowed(?float $lat, ?float $lng): bool
    {
        $settings = $this->settings();

        // Location check applies only when the school coordinates are set.
        if ($settings['demo'] || $settings['lat'] === null || $settings['lng'] === null) {
            return true;
        }

        if ($lat === null || $lng === null) {
            Notification::make()->danger()->title(__('Location is required. Please allow location access in your browser.'))->send();

            return false;
        }

        $distance = Geo::distanceMeters($lat, $lng, $settings['lat'], $settings['lng']);

        if ($distance > $settings['radius']) {
            Notification::make()
                ->danger()
                ->title(__('You are :distance m from school. Check-in is allowed within :radius m.', ['distance' => number_format($distance), 'radius' => $settings['radius']]))
                ->send();

            return false;
        }

        return true;
    }

    private function teacher(): Teacher
    {
        return once(fn () => Teacher::where('user_id', auth()->id())->firstOrFail());
    }
}
