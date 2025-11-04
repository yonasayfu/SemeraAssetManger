<?php

namespace App\Http\Controllers;

use App\Services\NotificationSettingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserNotificationSettingsController extends Controller
{
    public function __construct(private NotificationSettingService $notificationSettingService)
    {
        $this->middleware('auth');
    }

    public function index(Request $request): Response
    {
        $user = $request->user();
        $preferences = $this->notificationSettingService->getPreferencesForUser($user);

        return Inertia::render('Profile/NotificationSettings', [
            'preferences' => $preferences,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $this->notificationSettingService->updatePreferences($user, $request->input('preferences'));

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
