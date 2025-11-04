<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\UserNotificationPreference;
use Illuminate\Support\Collection;

class NotificationSettingService
{
    public function getPreferencesForUser(Staff $user): Collection
    {
        return $user->notificationPreferences()->get();
    }

    public function updatePreferences(Staff $user, array $preferences): void
    {
        foreach ($preferences as $preferenceData) {
            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $preferenceData['notification_type'],
                    'channel' => $preferenceData['channel'],
                ],
                ['enabled' => $preferenceData['enabled']]
            );
        }
    }

    public function togglePreference(Staff $user, string $notificationType, string $channel, bool $enabled): void
    {
        UserNotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'channel' => $channel,
            ],
            ['enabled' => $enabled]
        );
    }
}
