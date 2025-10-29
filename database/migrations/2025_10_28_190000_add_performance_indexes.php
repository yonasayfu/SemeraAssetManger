<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Assets table indexes
        Schema::table('assets', function (Blueprint $table) {
            $table->index('status', 'assets_status_idx');
            $table->index('site_id', 'assets_site_id_idx');
            $table->index('location_id', 'assets_location_id_idx');
            $table->index('category_id', 'assets_category_id_idx');
        });

        // Checkouts table indexes
        Schema::table('checkouts', function (Blueprint $table) {
            $table->index('asset_id', 'checkouts_asset_id_idx');
            $table->index('due_at', 'checkouts_due_at_idx');
            $table->index('status', 'checkouts_status_idx');
        });

        // Leases table indexes
        Schema::table('leases', function (Blueprint $table) {
            $table->index('asset_id', 'leases_asset_id_idx');
            $table->index('end_at', 'leases_end_at_idx');
            $table->index('status', 'leases_status_idx');
        });

        // Maintenances table indexes
        Schema::table('maintenances', function (Blueprint $table) {
            $table->index('asset_id', 'maintenances_asset_id_idx');
            $table->index('scheduled_for', 'maintenances_scheduled_for_idx');
            $table->index('status', 'maintenances_status_idx');
        });

        // Alerts table indexes (PostgreSQL-safe: IF NOT EXISTS)
        if (Schema::hasTable('alerts')) {
            DB::statement('CREATE INDEX IF NOT EXISTS alerts_type_idx ON alerts (type)');
            DB::statement('CREATE INDEX IF NOT EXISTS alerts_due_date_idx ON alerts (due_date)');
            DB::statement('CREATE INDEX IF NOT EXISTS alerts_sent_idx ON alerts (sent)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Assets table
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_status_idx');
            $table->dropIndex('assets_site_id_idx');
            $table->dropIndex('assets_location_id_idx');
            $table->dropIndex('assets_category_id_idx');
        });

        // Checkouts table
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropIndex('checkouts_asset_id_idx');
            $table->dropIndex('checkouts_due_at_idx');
            $table->dropIndex('checkouts_status_idx');
        });

        // Leases table
        Schema::table('leases', function (Blueprint $table) {
            $table->dropIndex('leases_asset_id_idx');
            $table->dropIndex('leases_end_at_idx');
            $table->dropIndex('leases_status_idx');
        });

        // Maintenances table
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex('maintenances_asset_id_idx');
            $table->dropIndex('maintenances_scheduled_for_idx');
            $table->dropIndex('maintenances_status_idx');
        });

        // Alerts table
        if (Schema::hasTable('alerts')) {
            DB::statement('DROP INDEX IF EXISTS alerts_type_idx');
            DB::statement('DROP INDEX IF EXISTS alerts_due_date_idx');
            DB::statement('DROP INDEX IF EXISTS alerts_sent_idx');
        }
    }
};
