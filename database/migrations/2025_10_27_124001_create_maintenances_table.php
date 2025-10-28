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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->enum('maintenance_type', ['Preventive', 'Corrective']);
            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['Open', 'Scheduled', 'In Progress', 'Completed', 'Cancelled', 'Overdue'])->default('Open');
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('vendor', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
