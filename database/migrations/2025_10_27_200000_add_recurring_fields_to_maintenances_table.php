<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('vendor');
            $table->string('recurrence_frequency', 20)->nullable()->after('is_recurring');
            $table->unsignedInteger('recurrence_interval')->nullable()->after('recurrence_frequency');
            $table->dateTime('next_scheduled_for')->nullable()->after('recurrence_interval');
            $table->dateTime('last_generated_at')->nullable()->after('next_scheduled_for');
            $table->decimal('labor_cost', 12, 2)->nullable()->after('cost');
            $table->decimal('parts_cost', 12, 2)->nullable()->after('labor_cost');

            $table->index(['is_recurring', 'next_scheduled_for'], 'maintenances_recurring_index');
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropIndex('maintenances_recurring_index');

            $table->dropColumn([
                'is_recurring',
                'recurrence_frequency',
                'recurrence_interval',
                'next_scheduled_for',
                'last_generated_at',
                'labor_cost',
                'parts_cost',
            ]);
        });
    }
};
