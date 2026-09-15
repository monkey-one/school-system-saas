<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\PaymentStatus;
use App\Enums\PPDBStatus;
use App\Filament\SchoolAdmin\Resources\LeaveRequestResource;
use App\Filament\SchoolAdmin\Resources\MessageResource;
use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource;
use App\Filament\SchoolAdmin\Resources\SppBillResource;
use App\Models\LeaveRequest;
use App\Models\Message;
use App\Models\PPDBRegistration;
use App\Models\SppBill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// "Needs attention" row on the school admin dashboard, each stat linking to
// the list where the work is done.
class PendingTasksWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make(__('PPDB awaiting review'), PPDBRegistration::where('status', PPDBStatus::PENDING)->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning')
                ->url(PPDBRegistrationResource::getUrl()),
            Stat::make(__('Leave requests pending'), LeaveRequest::where('status', 'pending')->count())
                ->icon('heroicon-o-envelope-open')
                ->color('warning')
                ->url(LeaveRequestResource::getUrl()),
            Stat::make(__('Overdue bills'), SppBill::where('status', PaymentStatus::OVERDUE)->count())
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->url(SppBillResource::getUrl()),
            Stat::make(__('Unread messages'), Message::where('recipient_id', auth()->id())->whereNull('read_at')->count())
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->url(MessageResource::getUrl()),
        ];
    }
}
