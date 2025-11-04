<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('staff')->onDelete('cascade');
            $table->string('name');
            $table->string('family'); // e.g., assets, maintenance, leases, etc.
            $table->json('definition_json'); // Stores the report's filters, columns, etc.
            $table->string('schedule_cron')->nullable(); // Cron expression for scheduling
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_reports');
    }
};
