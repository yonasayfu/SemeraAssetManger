<?php

namespace App\Support\Staff;

use App\Models\ActivityLog;
use App\Models\Staff;

trait SyncsStaffAssignment
{
    protected function syncStaffAssignment(Staff $user, ?int $staffId): void
    {
        $previousStaff = $user->staff;
        $previousStaffId = $previousStaff?->id;

        if ($previousStaff && ($staffId === null || $previousStaffId !== $staffId)) {
            $previousStaff->user()->dissociate();
            $previousStaff->save();
        }

        $currentStaff = null;

        if ($staffId) {
            if ($previousStaffId === $staffId) {
                $currentStaff = $previousStaff;
            } else {
                $currentStaff = Staff::find($staffId);

                if ($currentStaff) {
                    $originalUserId = $currentStaff->user_id;

                    if ($originalUserId && $originalUserId !== $user->id) {
                        $currentStaff->user()->dissociate();
                        $currentStaff->save();
                    }

                    $currentStaff->user()->associate($user);
                    $currentStaff->save();

                    ActivityLog::record(
                        auth()->id(),
                        $currentStaff,
                        'staff_profile_link.updated',
                        'Staff profile link updated',
                        [
                            'before' => ['staff_id' => $originalUserId],
                            'after' => ['staff_id' => $user->id],
                        ]
                    );
                }
            }
        }

        if ($previousStaff && $previousStaffId !== $staffId) {
            ActivityLog::record(
                auth()->id(),
                $user,
                'staff_profile_link.removed',
                'Staff profile link removed',
                [
                    'before' => ['staff_id' => $user->id],
                    'after' => ['staff_id' => null],
                ]
            );
        }

        $user->unsetRelation('staff');

        if ($staffId && $staffId === $previousStaffId) {
            $user->setRelation('staff', $previousStaff);
        } elseif ($currentStaff) {
            $user->setRelation('staff', $currentStaff);
        }

        $currentStaffId = $user->staff?->id;

        if ($previousStaffId !== $currentStaffId) {
            ActivityLog::record(
                auth()->id(),
                $user,
                'staff_link.updated',
                'Linked staff profile updated',
                [
                    'before' => ['staff_id' => $previousStaffId],
                    'after' => ['staff_id' => $currentStaffId],
                ]
            );
        }
    }
}
