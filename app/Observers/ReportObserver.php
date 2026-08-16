<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\User;
use Filament\Notifications\Notification;

class ReportObserver
{
    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        if (! $report->wasChanged('status')) {
            return;
        }

        match ($report->status) {
            'submitted' => $this->notifySubmitted($report),
            'approved' => $this->notifyApproved($report),
            default => null,
        };
    }

    private function notifySubmitted(Report $report): void
    {
        $assessors = User::whereHas('roles', fn ($query) => $query->where('name', 'assessor'))->get();

        if ($assessors->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('ມີບົດລາຍງານໃໝ່ລໍຖ້າການປະເມີນ')
            ->body("ບົດລາຍງານຂອງ {$report->department->name} - {$report->indicator->name}")
            ->info()
            ->sendToDatabase($assessors);
    }

    private function notifyApproved(Report $report): void
    {
        $departmentStaff = User::whereHas('roles', fn ($query) => $query->where('name', 'department-staff'))
            ->where('department_id', $report->department_id)
            ->get();

        if ($departmentStaff->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('ບົດລາຍງານຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ')
            ->body($report->indicator->name)
            ->success()
            ->sendToDatabase($departmentStaff);
    }
}
