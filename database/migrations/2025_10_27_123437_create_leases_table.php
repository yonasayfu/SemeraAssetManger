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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('lessee_type'); // customer|department
            $table->unsignedBigInteger('lessee_id');
            $table->date('start_at');
            $table->date('end_at');
            $table->decimal('rate_minor', 15, 2);
            $table->string('currency', 10);
            $table->text('terms')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
