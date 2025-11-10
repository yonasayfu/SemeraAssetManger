<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Lab404\Impersonate\Services\ImpersonateManager;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        $impersonateManager = app(ImpersonateManager::class);
        $isImpersonating = $impersonateManager->isImpersonating();
        $impersonator = $isImpersonating ? $impersonateManager->getImpersonator() : null;
        $impersonatedUser = $isImpersonating ? $request->user() : null;

        $company = \App\Models\Company::first();
        $logoUrl = null;
        $sidebarLogoUrl = null;
        if ($company && !empty($company->logo)) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo)) {
                    $logoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo);
                }
            } catch (\Throwable $_) {
                // ignore storage errors
            }
        }
        if ($company && !empty($company->sidebar_logo)) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($company->sidebar_logo)) {
                    $sidebarLogoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($company->sidebar_logo);
                }
            } catch (\Throwable $_) {
                // ignore storage errors
            }
        }

        return [
            ...parent::share($request),
            // App display name prefers company name when available
            'name' => $company?->name ?: config('app.name'),
            // Branding shared to the frontend
            'branding' => [
                'name' => $company?->name ?: config('app.name'),
                'logo_url' => $logoUrl ?: '/images/asset-logo.svg',
                'sidebar_logo_url' => $sidebarLogoUrl ?: null,
                'color' => $company?->brand_color,
                'secondary' => $company?->secondary_color,
                'logo_padding' => $company?->brand_logo_padding ?? 0,
                'logo_fit' => $company?->brand_logo_fit ?? 'contain',
                'logo_scale' => $company?->brand_logo_scale ?? 100,
                'logo_offset_x' => $company?->brand_logo_offset_x ?? 0,
                'logo_offset_y' => $company?->brand_logo_offset_y ?? 0,
                'sidebar_logo_height' => $company?->sidebar_logo_height,
                'sidebar_logo_width' => $company?->sidebar_logo_width,
            ],
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'staff' => $request->user(),
                'roles' => $request->user()
                    ? $request->user()->getRoleNames()->values()->toArray()
                    : [],
                'permissions' => $request->user()
                    ? $request->user()->getAllPermissions()->pluck('name')->values()->toArray()
                    : [],
                'can' => [
                    'viewStaff' => $request->user()?->can('staff.view') ?? false,
                    'manageStaff' => $request->user()?->can('staff.manage') ?? false,
                    'manageRoles' => $request->user()
                        ? ($request->user()->can('roles.manage') || $request->user()->can('staff.manage'))
                        : false,
                ],
            ],
            'impersonation' => [
                'active' => $isImpersonating,
                'impersonator' => $impersonator ? [
                    'id' => $impersonator->getKey(),
                    'name' => $impersonator->name,
                    'email' => $impersonator->email,
                ] : null,
                'target' => $impersonatedUser ? [
                    'id' => $impersonatedUser->getKey(),
                    'name' => $impersonatedUser->name,
                    'email' => $impersonatedUser->email,
                ] : null,
            ],
            'navigation' => [
                'sidebar' => config('sidebar.groups', []),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            // Share flash messages for Toast component
            'flash' => [
                'banner' => fn () => $request->session()->get('flash.banner'),
                'bannerStyle' => fn () => $request->session()->get('flash.bannerStyle'),
            ],
        ];
    }
}
