<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->index('status', 'idx_assets_status');
                $table->index('category_id', 'idx_assets_category');
                $table->index('site_id', 'idx_assets_site');
                $table->index('location_id', 'idx_assets_location');
                $table->index('department_id', 'idx_assets_department');
                $table->index('staff_id', 'idx_assets_staff');
            });
        }

        if (Schema::hasTable('checkouts')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->index('asset_id', 'idx_checkouts_asset');
                $table->index('assignee_id', 'idx_checkouts_assignee');
                $table->index('status', 'idx_checkouts_status');
                $table->index('checked_out_at', 'idx_checkouts_checked_out_at');
                $table->index('due_at', 'idx_checkouts_due_at');
            });
        }

        if (Schema::hasTable('leases')) {
            Schema::table('leases', function (Blueprint $table) {
                $table->index('asset_id', 'idx_leases_asset');
                $table->index('lessee_id', 'idx_leases_lessee');
                $table->index('status', 'idx_leases_status');
                $table->index('start_at', 'idx_leases_start_at');
                $table->index('end_at', 'idx_leases_end_at');
            });
        }

        if (Schema::hasTable('maintenances')) {
            Schema::table('maintenances', function (Blueprint $table) {
                $table->index('asset_id', 'idx_maint_asset');
                $table->index('status', 'idx_maint_status');
                $table->index('maintenance_type', 'idx_maint_type');
                $table->index('scheduled_for', 'idx_maint_scheduled_for');
            });
        }

        if (Schema::hasTable('reservations')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('asset_id', 'idx_res_asset');
                $table->index('requester_id', 'idx_res_requester');
                $table->index('status', 'idx_res_status');
                $table->index('start_at', 'idx_res_start_at');
            });
        }

        if (Schema::hasTable('warranties')) {
            Schema::table('warranties', function (Blueprint $table) {
                $table->index('asset_id', 'idx_warr_asset');
                if (Schema::hasColumn('warranties', 'expiry_date')) {
                    $table->index('expiry_date', 'idx_warr_expiry_date');
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id'], 'idx_activity_subject');
                $table->index('causer_id', 'idx_activity_causer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropIndex('idx_assets_status');
                $table->dropIndex('idx_assets_category');
                $table->dropIndex('idx_assets_site');
                $table->dropIndex('idx_assets_location');
                $table->dropIndex('idx_assets_department');
                $table->dropIndex('idx_assets_staff');
            });
        }
        if (Schema::hasTable('checkouts')) {
            Schema::table('checkouts', function (Blueprint $table) {
                $table->dropIndex('idx_checkouts_asset');
                $table->dropIndex('idx_checkouts_assignee');
                $table->dropIndex('idx_checkouts_status');
                $table->dropIndex('idx_checkouts_checked_out_at');
                $table->dropIndex('idx_checkouts_due_at');
            });
        }
        if (Schema::hasTable('leases')) {
            Schema::table('leases', function (Blueprint $table) {
                $table->dropIndex('idx_leases_asset');
                $table->dropIndex('idx_leases_lessee');
                $table->dropIndex('idx_leases_status');
                $table->dropIndex('idx_leases_start_at');
                $table->dropIndex('idx_leases_end_at');
            });
        }
        if (Schema::hasTable('maintenances')) {
            Schema::table('maintenances', function (Blueprint $table) {
                $table->dropIndex('idx_maint_asset');
                $table->dropIndex('idx_maint_status');
                $table->dropIndex('idx_maint_type');
                $table->dropIndex('idx_maint_scheduled_for');
            });
        }
        if (Schema::hasTable('reservations')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropIndex('idx_res_asset');
                $table->dropIndex('idx_res_requester');
                $table->dropIndex('idx_res_status');
                $table->dropIndex('idx_res_start_at');
            });
        }
        if (Schema::hasTable('warranties')) {
            Schema::table('warranties', function (Blueprint $table) {
                $table->dropIndex('idx_warr_asset');
                if (Schema::hasColumn('warranties', 'expiry_date')) {
                    $table->dropIndex('idx_warr_expiry_date');
                }
            });
        }
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('idx_activity_subject');
                $table->dropIndex('idx_activity_causer');
            });
        }
    }
};

