<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GeneralSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile/GeneralSettings', [
            'settings' => [
                'theme' => $user->theme,
                'locale' => $user->locale,
                'timezone' => $user->timezone,
            ],
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'theme' => 'required|string|in:light,dark,system',
            'locale' => 'required|string|in:en,es,fr', // Example locales
            'timezone' => 'required|string|timezone',
        ]);

        $user->update([
            'theme' => $request->input('theme'),
            'locale' => $request->input('locale'),
            'timezone' => $request->input('timezone'),
        ]);

        return back()->with('success', 'General settings updated successfully.');
    }
}
