<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // If legacy column exists, migrate data into staff_id and drop it safely.
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'assigned_to')) {
            Schema::table('assets', function (Blueprint $table) {
                if (! Schema::hasColumn('assets', 'staff_id')) {
                    $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
                }
            });

            // Backfill values where staff_id is null
            DB::table('assets')->whereNull('staff_id')->update([
                'staff_id' => DB::raw('assigned_to'),
            ]);

            // Drop legacy FK and column if possible
            try {
                Schema::table('assets', function (Blueprint $table) {
                    // Attempt to drop foreign key by column
                    try { $table->dropForeign(['assigned_to']); } catch (\Throwable $e) {}
                    // Finally drop the column
                    $table->dropColumn('assigned_to');
                });
            } catch (\Throwable $e) {
                // Ignore if already handled or not present
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            // Recreate assigned_to for rollback if needed
            Schema::table('assets', function (Blueprint $table) {
                if (! Schema::hasColumn('assets', 'assigned_to')) {
                    $table->unsignedBigInteger('assigned_to')->nullable()->after('department_id');
                    try { $table->foreign('assigned_to')->references('id')->on('staff')->nullOnDelete(); } catch (\Throwable $e) {}
                }
            });

            // Copy back from staff_id when present
            if (Schema::hasColumn('assets', 'staff_id')) {
                DB::table('assets')->update([
                    'assigned_to' => DB::raw('staff_id'),
                ]);
            }
        }
    }
};

