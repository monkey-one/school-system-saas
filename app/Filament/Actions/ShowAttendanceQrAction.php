<?php

namespace App\Filament\Actions;

use App\Models\AttendanceSession;
use App\Services\QRCodeService;
use Filament\Tables\Actions\Action;

// Table action shared by the school admin and teacher panels: shows a QR code
// (signed JWT, valid 15 minutes) that students scan to check in. Each time the
// modal opens a fresh token is generated, so a photographed QR code expires.
class ShowAttendanceQrAction
{
    public static function make(): Action
    {
        return Action::make('showQr')
            ->label(__('Show QR'))
            ->icon('heroicon-o-qr-code')
            ->color('info')
            ->visible(fn (AttendanceSession $record): bool => $record->status === 'open')
            ->modalHeading(__('Attendance QR Code'))
            ->modalWidth('lg')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Close'))
            ->modalContent(function (AttendanceSession $record) {
                $qr = app(QRCodeService::class)->generateAttendanceQR($record->teacher_id, $record->id, route('attendance.scan'));

                $record->forceFill([
                    'qr_token' => substr(hash('sha256', $qr['token']), 0, 40),
                    'qr_generated_at' => now(),
                    'qr_expires_at' => $qr['expires_at'],
                ])->saveQuietly();

                return view('filament.attendance-qr', [
                    'session' => $record->loadMissing('classroomSubject.classroom', 'classroomSubject.subject')->loadCount('studentAttendances'),
                    'qr' => $qr,
                ]);
            });
    }
}
