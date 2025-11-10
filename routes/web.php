<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCheckoutController;
use App\Http\Controllers\AssetCheckinController;
use App\Http\Controllers\AssetDisposeController;
use App\Http\Controllers\AssetImportController;
use App\Http\Controllers\AssetLeaseController;
use App\Http\Controllers\AssetLeaseReturnController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\AssetMoveController;
use App\Http\Controllers\AssetListController;
use App\Http\Controllers\AssetOperationController;
use App\Http\Controllers\AssetReserveController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuditListController;

use App\Http\Controllers\AuditScanController;
use App\Http\Controllers\AuditWizardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DocumentGalleryController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ImageGalleryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Mailbox\MailpitWebhookController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceListController;
use App\Http\Controllers\ManageDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\PendingApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Report\AssetReportController;
use App\Http\Controllers\Report\AuditReportListController;
use App\Http\Controllers\Report\AutomatedReportController;
use App\Http\Controllers\Report\CheckoutReportController;
use App\Http\Controllers\Report\CustomReportController;
use App\Http\Controllers\Report\LeasedAssetReportController;
use App\Http\Controllers\Report\MaintenanceReportController;
use App\Http\Controllers\Report\RunReportController;
use App\Http\Controllers\Report\ReservationReportController;
use App\Http\Controllers\Report\OtherReportController;
use App\Http\Controllers\Report\StatusReportController;
use App\Http\Controllers\Report\TransactionReportController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StoreAssetCheckoutController;
use App\Http\Controllers\StoreAssetCheckinController;
use App\Http\Controllers\StoreAssetDisposeController;
use App\Http\Controllers\StoreAssetImportController;
use App\Http\Controllers\StoreAssetExportPreferencesController;
use App\Http\Controllers\AssetImportPreviewController;
use App\Http\Controllers\AssetImportDryRunController;
use App\Http\Controllers\StoreAssetLeaseController;
use App\Http\Controllers\StoreAssetLeaseReturnController;
use App\Http\Controllers\StoreAssetMoveController;
use App\Http\Controllers\StoreAssetReserveController;
use App\Http\Controllers\StaffImportController;
use App\Http\Controllers\SiteImportController;
use App\Http\Controllers\LocationImportController;
use App\Http\Controllers\CategoryImportController;
use App\Http\Controllers\DepartmentImportController;
use App\Http\Controllers\MaintenanceImportController;
use App\Http\Controllers\WarrantyImportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\TwoFactorEmailRecoveryController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\WarrantyListController;
use App\Http\Controllers\Alert\AssetsDueController;
use App\Http\Controllers\Alert\AssetsPastDueController;
use App\Http\Controllers\Alert\LeasesExpiringController;
use App\Http\Controllers\Alert\MaintenanceDueController;
use App\Http\Controllers\Alert\MaintenanceOverdueController;
use App\Http\Controllers\Alert\WarrantiesExpiringController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\Clearance\MyAssetsController;
use App\Http\Controllers\Clearance\ClearanceController as StaffClearanceController;
use App\Http\Controllers\Clearance\Admin\ClearanceAdminController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home page
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Impersonation routes (if not in console)
if (! app()->runningInConsole()) {
    Route::impersonate();
}

// Mailpit webhook for local environment
if (app()->environment('local')) {
    Route::post('mailpit/webhook', MailpitWebhookController::class)
        ->middleware(['mailpit.signature'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('mailpit.webhook');
}

// Routes requiring authentication
Route::middleware('auth')->group(function () {

    // Onboarding pending approval
    Route::get('onboarding/pending-approval', PendingApprovalController::class)->name('onboarding.pending');

    // Routes requiring verified email and approval
    Route::middleware(['verified', 'approved'])->group(function () {

        // Dashboard and global search
        Route::get('global-search', GlobalSearchController::class)->name('global-search');
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        // Two-factor authentication profile settings
        Route::get('/user/two-factor-authentication', function () {
            return Inertia::render('Profile/TwoFactorAuthentication');
        })->name('profile.two-factor-authentication');

        // Notification Preferences
        Route::get('/profile/notification-preferences', [\App\Http\Controllers\UserNotificationSettingsController::class, 'index'])
            ->name('profile.notification-preferences.index');
        Route::post('/profile/notification-preferences', [\App\Http\Controllers\UserNotificationSettingsController::class, 'update'])
            ->name('profile.notification-preferences.update');

        // General Settings
        Route::get('/profile/general-settings', [\App\Http\Controllers\GeneralSettingsController::class, 'index'])
            ->name('profile.general-settings.index');
        Route::post('/profile/general-settings', [\App\Http\Controllers\GeneralSettingsController::class, 'update'])
            ->name('profile.general-settings.update');

        // Mailbox for local environment
        if (app()->environment('local')) {
            Route::prefix('mailbox')->name('mailbox.')->middleware('permission:mailbox.view')->group(function () {
                Route::get('/', [MailboxController::class, 'index'])->name('index');
                Route::get('/{message}', [MailboxController::class, 'show'])->name('show');
                Route::post('/{message}/process', [MailboxController::class, 'process'])->name('process')
                    ->middleware('permission:mailbox.process');
            });
        }

        // Data Exports (Admin only)
        Route::prefix('exports')->name('exports.')->middleware('role:Admin')->group(function () {
            Route::get('/', [DataExportController::class, 'index'])->name('index');
            Route::get('/{export}', [DataExportController::class, 'download'])->name('download');
            Route::delete('/{export}', [DataExportController::class, 'destroy'])->name('destroy');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
            Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
            Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        });

        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])
            ->middleware('permission:activity-logs.view')
            ->name('activity-logs.index');

        // Two-factor email recovery
        Route::post('/two-factor-email-recovery/send', [TwoFactorEmailRecoveryController::class, 'sendRecoveryCode'])->name('two-factor-email-recovery.send');
        Route::post('/two-factor-email-recovery/verify', [TwoFactorEmailRecoveryController::class, 'verifyRecoveryCode'])->name('two-factor-email-recovery.verify');

        // Staff Management
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::middleware('permission:staff.view')->group(function () {
                Route::get('/', [StaffController::class, 'index'])->name('index');
                Route::get('/export', [StaffController::class, 'export'])->name('export')->middleware('role:Admin');
                Route::get('/{staff}', [StaffController::class, 'show'])->name('show');
            });

            Route::get('/create', [StaffController::class, 'create'])->name('create')->middleware('permission:staff.create');
            Route::post('/', [StaffController::class, 'store'])->name('store')->middleware('permission:staff.create');
            Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('edit')->middleware('permission:staff.update');
            Route::put('/{staff}', [StaffController::class, 'update'])->name('update')->middleware('permission:staff.update');
            Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('destroy')->middleware('permission:staff.delete');
        });

        // User Management (Admin Accounts) - moved under /admin/staff to avoid conflict with Staff directory
        Route::prefix('admin/staff')->name('admin.staff.')->middleware('permission:users.manage')->group(function () {
            Route::get('/', [StaffManagementController::class, 'index'])->name('index');
            Route::get('/export', [StaffManagementController::class, 'export'])->name('export')->middleware('role:Admin');
            Route::get('/create', [StaffManagementController::class, 'create'])->name('create');
            Route::post('/', [StaffManagementController::class, 'store'])->name('store');
            Route::get('/{user}', [StaffManagementController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [StaffManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [StaffManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [StaffManagementController::class, 'destroy']);
        });

        // Role Management
        Route::resource('roles', RoleManagementController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->middleware('permission:roles.manage|staff.manage');

        // Sample Pages
        Route::prefix('samples')->name('samples.')->group(function () {
            Route::get('/', function () {
                return Inertia::render('samples/Index');
            })->name('index');

            Route::get('/admin', function () {
                return Inertia::render('samples/SampleAdminPage');
            })->middleware('role:Admin')->name('admin');

            Route::get('/manager', function () {
                return Inertia::render('samples/SampleManagerPage');
            })->middleware('role:Manager')->name('manager');

            Route::get('/technician', function () {
                return Inertia::render('samples/SampleTechnicianPage');
            })->middleware('role:Technician')->name('technician');

            Route::get('/external', function () {
                return Inertia::render('samples/SampleExternalPage');
            })->middleware('role:External|ReadOnly')->name('external');
        });

        // Setup Module
        Route::prefix('setup')->name('setup.')->middleware('permission:setup.manage')->group(function () {
            Route::resource('companies', CompanyController::class);
            Route::resource('sites', SiteController::class);
            Route::resource('locations', LocationController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('departments', DepartmentController::class);
            Route::get('manage-dashboard', ManageDashboardController::class)->name('manage-dashboard.index');
            Route::post('manage-dashboard', [ManageDashboardController::class, 'store'])->name('manage-dashboard.store');
        });

        // Setup root should not 404; send to a sensible default
        Route::get('/setup', function () {
            return redirect()->route('setup.companies.index');
        })->name('setup.index');
        // Legacy singular path redirect
        Route::get('/setup/company', function () {
            return redirect()->route('setup.companies.index');
        })->name('setup.company.legacy');

        // Asset Base Routes
        Route::prefix('assets')->name('assets.')->middleware('permission:assets.view')->group(function () {
            Route::get('/', [AssetController::class, 'index'])->name('index');
            Route::get('/create', [AssetController::class, 'create'])->middleware('permission:assets.create')->name('create');
            Route::post('/', [AssetController::class, 'store'])->middleware('permission:assets.create')->name('store');
            Route::get('/export', [AssetController::class, 'export'])->name('export');
            Route::post('/export-preferences', StoreAssetExportPreferencesController::class)->name('export-preferences');
            Route::get('/import', AssetImportController::class)->name('import');
            Route::get('/import/template', [AssetImportController::class, 'template'])->name('import.template');
            Route::post('/import/preview', AssetImportPreviewController::class)->name('import.preview');
            Route::post('/import/dry-run', AssetImportDryRunController::class)->name('import.dry-run');
            Route::get('/import/report/{token}', \App\Http\Controllers\AssetImportReportController::class)->name('import.report');
            Route::post('/import/presets', [\App\Http\Controllers\AssetImportPresetController::class, 'store'])->name('import.presets.store');
            Route::delete('/import/presets/{preset}', [\App\Http\Controllers\AssetImportPresetController::class, 'destroy'])->name('import.presets.destroy');
            Route::post('/import/jobs', [\App\Http\Controllers\AssetImportJobController::class, 'store'])->name('import.jobs.store');
            Route::get('/import/jobs/{job}', [\App\Http\Controllers\AssetImportJobController::class, 'show'])->name('import.jobs.show');
            Route::post('/import/jobs/{job}/cancel', [\App\Http\Controllers\AssetImportJobController::class, 'cancel'])->name('import.jobs.cancel');
            // UX fallback: visiting these endpoints by GET should redirect back to the import page
            Route::get('/import/preview', function () {
                return redirect()->route('assets.import');
            });
            Route::get('/import/dry-run', function () {
                return redirect()->route('assets.import');
            });
            Route::post('/import', StoreAssetImportController::class)->name('import.store');

            // Asset Operation Select Pages
            Route::get('checkout-select', [AssetOperationController::class, 'select'])->name('checkout.select')->defaults('operation', 'checkout');
            Route::get('checkin-select', [AssetOperationController::class, 'select'])->name('checkin.select')->defaults('operation', 'checkin');
            Route::get('lease-select', [AssetOperationController::class, 'select'])->name('lease.select')->defaults('operation', 'lease');
            Route::get('lease-return-select', [AssetOperationController::class, 'select'])->name('lease-return.select')->defaults('operation', 'lease-return');
            Route::get('dispose-select', [AssetOperationController::class, 'select'])->name('dispose.select')->defaults('operation', 'dispose');
            Route::get('maintenance-select', [AssetOperationController::class, 'select'])->name('maintenance.select')->defaults('operation', 'maintenance');
            Route::get('move-select', [AssetOperationController::class, 'select'])->name('move.select')->defaults('operation', 'move');
            Route::get('reserve-select', [AssetOperationController::class, 'select'])->name('reserve.select')->defaults('operation', 'reserve');
        });

        // Clearance (Staff)
        Route::middleware('permission:clearances.view')->group(function () {
            Route::get('/my/assets', MyAssetsController::class)->name('my.assets');
            Route::get('/clearances', [StaffClearanceController::class, 'index'])->name('clearances.index');
            Route::post('/clearances', [StaffClearanceController::class, 'store'])->name('clearances.store')->middleware('permission:clearances.request');
            Route::get('/clearances/{clearance}', [StaffClearanceController::class, 'show'])->name('clearances.show');
            Route::put('/clearances/{clearance}', [StaffClearanceController::class, 'update'])->name('clearances.update');
            Route::post('/clearances/{clearance}/submit', [StaffClearanceController::class, 'submit'])->name('clearances.submit');
            Route::get('/clearances/{clearance}/pdf', [StaffClearanceController::class, 'pdf'])->name('clearances.pdf');
        });

        // Clearance (Admin)
        Route::prefix('admin')->middleware('permission:clearances.manage')->group(function () {
            Route::get('/clearances', [ClearanceAdminController::class, 'index'])->name('admin.clearances.index');
            Route::get('/clearances/{clearance}', [ClearanceAdminController::class, 'show'])->name('admin.clearances.show');
            Route::put('/clearances/{clearance}', [ClearanceAdminController::class, 'update'])->name('admin.clearances.update');
            Route::post('/clearances/{clearance}/approve', [ClearanceAdminController::class, 'approve'])->name('admin.clearances.approve')->middleware('permission:clearances.approve');
            Route::post('/clearances/{clearance}/reject', [ClearanceAdminController::class, 'reject'])->name('admin.clearances.reject');
            Route::get('/clearances/{clearance}/pdf', [ClearanceAdminController::class, 'pdf'])->name('admin.clearances.pdf');
        });

        // Asset Specific Routes (requiring {asset} parameter)
        Route::prefix('assets/{asset}')->middleware('permission:assets.view')->group(function () {
            Route::get('/', [AssetController::class, 'show'])->name('show');
            Route::get('/edit', [AssetController::class, 'edit'])->middleware('permission:assets.update')->name('edit');
            Route::put('/', [AssetController::class, 'update'])->middleware('permission:assets.update')->name('update');
            Route::delete('/', [AssetController::class, 'destroy'])->middleware('permission:assets.delete')->name('destroy');

            Route::get('checkout', AssetCheckoutController::class)->name('checkout.create');
            Route::post('checkout', StoreAssetCheckoutController::class)->name('checkout.store');
            Route::get('checkin', AssetCheckinController::class)->name('checkin.create');
            Route::post('checkin', StoreAssetCheckinController::class)->name('checkin.store');
            Route::get('lease', AssetLeaseController::class)->name('lease.create');
            Route::post('lease', StoreAssetLeaseController::class)->name('lease.store');
            Route::get('lease-return', AssetLeaseReturnController::class)->name('lease-return.create');
            Route::post('lease-return', StoreAssetLeaseReturnController::class)->name('lease-return.store');
            Route::get('dispose', AssetDisposeController::class)->name('dispose.create');
            Route::post('dispose', StoreAssetDisposeController::class)->name('dispose.store');
            Route::get('move', AssetMoveController::class)->name('move.create');
            Route::post('move', StoreAssetMoveController::class)->name('move.store');
            Route::get('reserve', AssetReserveController::class)->name('reserve.create');
            Route::post('reserve', StoreAssetReserveController::class)->name('reserve.store');
            Route::resource('maintenance', AssetMaintenanceController::class); // Resource routes for asset-specific maintenance

            Route::prefix('tabs')->name('tabs.')->group(function () {
                Route::get('details', [AssetController::class, 'details'])->name('details');
                Route::get('history', [AssetController::class, 'history'])->name('history');
                Route::get('photos', [AssetController::class, 'photos'])->name('photos');
                Route::post('photos', [AssetController::class, 'uploadPhoto'])->name('photos.store')->middleware('permission:assets.update');
                Route::patch('photos/{photo}', [AssetController::class, 'updatePhoto'])->name('photos.update')->middleware('permission:assets.update');
                Route::delete('photos/{photo}', [AssetController::class, 'deletePhoto'])->name('photos.destroy')->middleware('permission:assets.update');
                Route::get('documents', [AssetController::class, 'documents'])->name('documents');
                Route::post('documents', [AssetController::class, 'uploadDocument'])->name('documents.store')->middleware('permission:assets.update');
                Route::patch('documents/{document}', [AssetController::class, 'updateDocument'])->name('documents.update')->middleware('permission:assets.update');
                Route::delete('documents/{document}', [AssetController::class, 'deleteDocument'])->name('documents.destroy')->middleware('permission:assets.update');
                Route::get('warranty', [AssetController::class, 'warranty'])->name('warranty');
                Route::post('warranty', [AssetController::class, 'storeWarranty'])->name('warranty.store')->middleware('permission:assets.update');
                Route::patch('warranty/{warranty}', [AssetController::class, 'updateWarranty'])->name('warranty.update')->middleware('permission:assets.update');
                Route::delete('warranty/{warranty}', [AssetController::class, 'destroyWarranty'])->name('warranty.destroy')->middleware('permission:assets.update');
                Route::get('maintenance', [AssetController::class, 'maintenance'])->name('maintenance');
                Route::get('reservations', [AssetController::class, 'reservations'])->name('reservations');
                Route::get('audits', [AssetController::class, 'audits'])->name('audits');
                Route::get('activity', [AssetController::class, 'activity'])->name('activity');
            });
        });

        Route::resource('maintenance', MaintenanceController::class)->middleware('permission:maintenance.view');
        Route::resource('warranties', WarrantyController::class)->middleware('permission:warranty.view');

        // Alerts Module (Base Index)
        Route::prefix('alerts')->name('alerts.')->middleware('permission:alerts.view')->group(function () {
            Route::get('/', App\Http\Controllers\AlertController::class)->name('index');
            Route::get('assets-due', AssetsDueController::class)->name('assets-due');
            Route::get('assets-past-due', AssetsPastDueController::class)->name('assets-past-due');
            Route::get('leases-expiring', LeasesExpiringController::class)->name('leases-expiring');
            Route::get('maintenance-due', MaintenanceDueController::class)->name('maintenance-due');
            Route::get('maintenance-overdue', MaintenanceOverdueController::class)->name('maintenance-overdue');
            Route::get('warranties-expiring', WarrantiesExpiringController::class)->name('warranties-expiring');
        });

        // Freshservice-style: Vendors, Products, Contracts, POs, Software (Phase 2 - basic CRUD)
        Route::middleware('permission:vendors.view')->group(function () {
            Route::resource('vendors', \App\Http\Controllers\VendorController::class)->except(['show']);
            Route::get('vendors/export', [\App\Http\Controllers\VendorController::class, 'export'])->name('vendors.export')->middleware('permission:reports.export');
        });
        Route::middleware('permission:products.view')->group(function () {
            Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['show']);
            Route::get('products/export', [\App\Http\Controllers\ProductController::class, 'export'])->name('products.export')->middleware('permission:reports.export');
        });
        Route::middleware('permission:contracts.view')->group(function () {
            Route::resource('contracts', \App\Http\Controllers\ContractController::class)->except(['show']);
            Route::get('contracts/board', [\App\Http\Controllers\ContractController::class, 'board'])->name('contracts.board');
            Route::get('contracts/export', [\App\Http\Controllers\ContractController::class, 'export'])->name('contracts.export')->middleware('permission:reports.export');
        });

        Route::middleware('permission:purchase-orders.view')->group(function () {
            Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class)->except(['show']);
            Route::post('purchase-orders/{purchase_order}/receive', [\App\Http\Controllers\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
            Route::get('purchase-orders/export', [\App\Http\Controllers\PurchaseOrderController::class, 'export'])->name('purchase-orders.export')->middleware('permission:reports.export');
        });
        Route::middleware('permission:software.view')->group(function () {
            Route::resource('software', \App\Http\Controllers\SoftwareController::class)->except(['show']);
            Route::get('software/export', [\App\Http\Controllers\SoftwareController::class, 'export'])->name('software.export')->middleware('permission:reports.export');
        });


        // Lists Module
        Route::prefix('lists')->name('lists.')->middleware('permission:lists.view')->group(function () {
            Route::get('assets', [AssetListController::class, 'index'])->name('assets');
            Route::get('assets/export', [AssetListController::class, 'export'])->name('assets.export')->middleware('permission:lists.export');
            Route::get('audits', [AuditListController::class, 'index'])->name('audits');
            Route::get('audits/export', [AuditListController::class, 'export'])->name('audits.export')->middleware('role:Admin');
            Route::get('maintenances', [MaintenanceListController::class, 'index'])->name('maintenances');
            Route::get('maintenances/export', [MaintenanceListController::class, 'export'])->name('maintenances.export')->middleware('role:Admin');
            Route::get('warranties', [WarrantyListController::class, 'index'])->name('warranties');
            Route::get('warranties/export', [WarrantyListController::class, 'export'])->name('warranties.export')->middleware('role:Admin');
        });

        Route::prefix('audits')->name('audits.')->group(function () {
            Route::get('wizard', [AuditWizardController::class, 'create'])->name('wizard');
            Route::post('wizard', [AuditWizardController::class, 'store'])->name('wizard.store');
            Route::get('{audit}/scan', [AuditScanController::class, 'show'])->name('scan');
            Route::post('{audit}/scan/assets/{auditAsset}', [AuditScanController::class, 'update'])->name('scan.assets.update');
            Route::post('{audit}/scan/complete', [AuditScanController::class, 'complete'])->name('scan.complete');
            Route::get('{audit}/scan/search', [AuditScanController::class, 'search'])->name('scan.search');
            Route::get('{audit}/report', [AuditReportListController::class, 'show'])->name('report');
            Route::get('{audit}/report/export', [AuditReportListController::class, 'export'])->name('report.export')->middleware('role:Admin');
        });

        // Reports Module
        Route::prefix('reports')->name('reports.')->middleware('permission:reports.view')->group(function () {
            Route::get('/', ReportController::class)->name('index');
            Route::get('automated', AutomatedReportController::class)->name('automated');
            Route::get('custom', CustomReportController::class)->name('custom');
            Route::get('assets', AssetReportController::class)->name('assets');
            Route::get('audits', AuditReportListController::class)->name('audits');
            Route::get('checkout', CheckoutReportController::class)->name('checkout');
            Route::get('leased-assets', LeasedAssetReportController::class)->name('leased-assets');
            Route::get('maintenance', MaintenanceReportController::class)->name('maintenance');
            Route::get('contracts', \App\Http\Controllers\Report\ContractReportController::class)->name('contracts');
            Route::get('purchase-orders', \App\Http\Controllers\Report\PurchaseOrderReportController::class)->name('purchase-orders');
            Route::get('software', \App\Http\Controllers\Report\SoftwareReportController::class)->name('software');
            Route::get('reservations', ReservationReportController::class)->name('reservations');
            Route::get('status', StatusReportController::class)->name('status');
            Route::get('transactions', TransactionReportController::class)->name('transactions');
            Route::get('others', \App\Http\Controllers\Report\OtherReportController::class)->name('others');
            Route::post('preview', [RunReportController::class, 'preview'])->name('preview');
            Route::post('export', [RunReportController::class, 'export'])->name('export')->middleware('role:Admin');
        });

        // Tools Module
        Route::prefix('tools')->name('tools.')->middleware('permission:tools.view')->group(function () {
            Route::get('import', [ToolsController::class, 'import'])->name('import')->middleware('role:Admin');
            Route::post('import/preview', \App\Http\Controllers\ToolsImportPreviewController::class)->name('import.preview')->middleware('role:Admin');
            Route::get('export', [ToolsController::class, 'export'])->name('export')->middleware('role:Admin');
            Route::post('import/staff', StaffImportController::class)->name('import.staff')->middleware('role:Admin');
            Route::post('import/sites', SiteImportController::class)->name('import.sites')->middleware('role:Admin');
            Route::post('import/locations', LocationImportController::class)->name('import.locations')->middleware('role:Admin');
            Route::post('import/categories', CategoryImportController::class)->name('import.categories')->middleware('role:Admin');
            Route::post('import/departments', DepartmentImportController::class)->name('import.departments')->middleware('role:Admin');
            Route::post('import/maintenances', MaintenanceImportController::class)->name('import.maintenances')->middleware('role:Admin');
            Route::post('import/warranties', WarrantyImportController::class)->name('import.warranties')->middleware('role:Admin');
            Route::post('import/vendors', \App\Http\Controllers\VendorImportController::class)->name('import.vendors')->middleware('role:Admin');
            Route::post('import/products', \App\Http\Controllers\ProductImportController::class)->name('import.products')->middleware('role:Admin');
            Route::post('import/contracts', \App\Http\Controllers\ContractImportController::class)->name('import.contracts')->middleware('role:Admin');
            Route::post('import/purchase-orders', \App\Http\Controllers\PurchaseOrderImportController::class)->name('import.purchase-orders')->middleware('role:Admin');
            Route::post('import/software', \App\Http\Controllers\SoftwareImportController::class)->name('import.software')->middleware('role:Admin');
            Route::get('documents', DocumentGalleryController::class)
                ->name('documents')
                ->withoutMiddleware('permission:tools.view')
                ->middleware('permission:assets.view');
            Route::get('images', ImageGalleryController::class)
                ->name('images')
                ->withoutMiddleware('permission:tools.view')
                ->middleware('permission:assets.view');
            // Gallery detail pages
            Route::get('documents/{document}', \App\Http\Controllers\DocumentDetailController::class)
                ->name('documents.show')
                ->withoutMiddleware('permission:tools.view')
                ->middleware('permission:assets.view');
            Route::get('images/{asset}', \App\Http\Controllers\ImageDetailController::class)
                ->name('images.show')
                ->withoutMiddleware('permission:tools.view')
                ->middleware('permission:assets.view');
            Route::resource('audits', AuditController::class);
        });

        // Tools root should not 404; send to import landing by default
        Route::get('/tools', function () {
            return redirect()->route('tools.import');
        })->name('tools.index');

        // Advanced Module
        Route::prefix('advanced')->name('advanced.')->middleware('permission:advanced.view')->group(function () {
            Route::resource('staff', StaffController::class);
            Route::resource('customers', CustomerController::class);
        });

        // Advanced root should not 404; send to default list
        Route::get('/advanced', function () {
            return redirect()->route('advanced.customers.index');
        })->name('advanced.index');

        // Help & Support Module
        Route::prefix('help')->name('help.')->group(function () {
            Route::get('about', StaticPageController::class)->name('about')->defaults('page', 'About Us');
            Route::get('contact', StaticPageController::class)->name('contact')->defaults('page', 'Contact Us');
            Route::get('terms', StaticPageController::class)->name('terms')->defaults('page', 'Terms of Service');
            Route::get('privacy', StaticPageController::class)->name('privacy')->defaults('page', 'Privacy Policy');
            Route::get('videos', StaticPageController::class)->name('videos')->defaults('page', 'Videos');
            Route::get('reviews', StaticPageController::class)->name('reviews')->defaults('page', 'User Reviews');
            Route::get('changelog', StaticPageController::class)->name('changelog')->defaults('page', 'Changelog');
            // Support page management (Admin)
            Route::resource('pages', \App\Http\Controllers\SupportPageController::class)
                ->middleware('role:Admin')
                ->except(['show']);
        });

        // Sidebar menu metadata (left here for UI composition reference only).
        // NOTE: We intentionally do NOT auto-generate placeholder routes from this metadata anymore.
        // Only explicit routes defined above are active to avoid accidental placeholder pages.
        $sidebarGroups = [
            [
                "id" => "main",
                "label" => "Main",
                "items" => [
                    [
                        "title" => "Alerts",
                        "href" => "/alerts",
                        "children" => [
                            ["title" => "Assets Due", "href" => "/alerts/assets-due"],
                            ["title" => "Assets Past Due", "href" => "/alerts/assets-past-due"],
                            ["title" => "Leases Expiring", "href" => "/alerts/leases-expiring"],
                            ["title" => "Maintenance Due", "href" => "/alerts/maintenance-due"],
                            ["title" => "Maintenance Overdue", "href" => "/alerts/maintenance-overdue"],
                            ["title" => "Warranties Expiring", "href" => "/alerts/warranties-expiring"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "assets",
                "label" => "Assets",
                "items" => [
                    [
                        "title" => "Assets",
                        "href" => "/assets",
                        "children" => [
                            // "List of Assets" is handled by assets.index
                            ["title" => "Add an Asset", "href" => "/assets/create"],
                            ["title" => "Check Out", "href" => "/assets/checkout-select"],
                            ["title" => "Check In", "href" => "/assets/checkin-select"],
                            ["title" => "Lease", "href" => "/assets/lease-select"],
                            ["title" => "Lease Return", "href" => "/assets/lease-return-select"],
                            ["title" => "Dispose", "href" => "/assets/dispose-select"],
                            ["title" => "Maintenance", "href" => "/assets/maintenance-select"],
                            ["title" => "Move", "href" => "/assets/move-select"],
                            ["title" => "Reserve", "href" => "/assets/reserve-select"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "lists",
                "label" => "Lists",
                "items" => [
                    [
                        "title" => "Lists",
                        "href" => "/lists",
                        "children" => [
                            ["title" => "List of Assets", "href" => "/lists/assets"], // To be implemented
                            ["title" => "List of Maintenances", "href" => "/lists/maintenances"],
                            ["title" => "List of Warranties", "href" => "/lists/warranties"],
                            ["title" => "List of Audits", "href" => "/lists/audits"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "reports",
                "label" => "Reports",
                "items" => [
                    [
                        "title" => "Reports",
                        "href" => "/reports",
                        "children" => [
                            ["title" => "Automated Reports", "href" => "/reports/automated"],
                            ["title" => "Custom Reports", "href" => "/reports/custom"],
                            ["title" => "Asset Reports", "href" => "/reports/assets"],
                            ["title" => "Audit Reports", "href" => "/reports/audits"],
                            ["title" => "Check-Out Reports", "href" => "/reports/checkout"],
                            ["title" => "Leased Asset Reports", "href" => "/reports/leased-assets"],
                            ["title" => "Maintenance Reports", "href" => "/reports/maintenance"],
                            ["title" => "Reservation Reports", "href" => "/reports/reservations"],
                            ["title" => "Status Reports", "href" => "/reports/status"],
                            ["title" => "Transaction Reports", "href" => "/reports/transactions"],
                            ["title" => "Other Reports", "href" => "/reports/others"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "tools",
                "label" => "Tools",
                "items" => [
                    [
                        "title" => "Tools",
                        "href" => "/tools",
                        "children" => [
                            ["title" => "Import", "href" => "/tools/import"], // To be implemented
                            ["title" => "Export", "href" => "/tools/export"], // To be implemented
                            ["title" => "Documents Gallery", "href" => "/tools/documents"],
                            ["title" => "Image Gallery", "href" => "/tools/images"],
                            ["title" => "Audit", "href" => "/tools/audit"], // To be implemented, currently part of resource audits
                        ],
                    ],
                ],
            ],
            [
                "id" => "advanced",
                "label" => "Advanced",
                "items" => [
                    [
                        "title" => "Advanced",
                        "href" => "/advanced",
                        "children" => [
                            ["title" => "Staff", "href" => "/advanced/staff"],
                            ["title" => "Customers", "href" => "/advanced/customers"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "setup",
                "label" => "Setup",
                "items" => [
                    [
                        "title" => "Setup",
                        "href" => "/setup",
                        "children" => [
                            ["title" => "Company Info", "href" => "/setup/companies"],
                            ["title" => "Sites", "href" => "/setup/sites"],
                            ["title" => "Locations", "href" => "/setup/locations"],
                            ["title" => "Categories", "href" => "/setup/categories"],
                            ["title" => "Departments", "href" => "/setup/departments"],
                            ["title" => "Manage Dashboard", "href" => "/setup/manage-dashboard"],
                        ],
                    ],
                ],
            ],
            [
                "id" => "help",
                "label" => "Help & Support",
                "items" => [
                    [
                        "title" => "Help / Support",
                        "href" => "/help",
                        "children" => [
                            ["title" => "About Us", "href" => "/help/about"],
                            ["title" => "Contact Us", "href" => "/help/contact"],
                            ["title" => "Terms of Service", "href" => "/help/terms"],
                            ["title" => "Privacy Policy", "href" => "/help/privacy"],
                            ["title" => "Videos", "href" => "/help/videos"],
                            ["title" => "User Reviews", "href" => "/help/reviews"],
                            ["title" => "Changelog", "href" => "/help/changelog"],
                        ],
                    ],
                ],
            ],
        ];

        // Removed: dynamic placeholder route generation

    }); // Closes Route::middleware(['verified', 'approved'])
}); // Closes Route::middleware(['auth', 'verified', 'approved'])


// Authentication routes
require __DIR__.'/auth.php';

// Settings routes
require __DIR__.'/settings.php';
