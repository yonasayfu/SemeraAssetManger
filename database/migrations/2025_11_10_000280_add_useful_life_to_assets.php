<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'in_service_date')) {
                $table->date('in_service_date')->nullable()->after('purchase_date');
            }
            if (!Schema::hasColumn('assets', 'useful_life_months')) {
                $table->unsignedSmallInteger('useful_life_months')->nullable()->after('in_service_date');
            }
            if (!Schema::hasColumn('assets', 'refresh_due_at')) {
                $table->date('refresh_due_at')->nullable()->after('useful_life_months');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'refresh_due_at')) {
                $table->dropColumn('refresh_due_at');
            }
            if (Schema::hasColumn('assets', 'useful_life_months')) {
                $table->dropColumn('useful_life_months');
            }
            if (Schema::hasColumn('assets', 'in_service_date')) {
                $table->dropColumn('in_service_date');
            }
        });
    }
};

