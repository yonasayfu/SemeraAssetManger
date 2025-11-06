<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete(); // subject of clearance
            $table->foreignId('requested_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('status')->default('draft'); // draft|submitted|in_review|approved|rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->string('hr_email')->nullable();
            $table->string('pdf_path')->nullable();
            $table->text('remarks')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['staff_id', 'status']);
        });

        Schema::create('clearance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clearance_id')->constrained('clearances')->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('action')->nullable(); // return|waive|keep|replace|pay
            $table->string('result')->nullable(); // ok|missing|damaged
            $table->text('condition_note')->nullable();
            $table->boolean('checked')->default(true);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['clearance_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_items');
        Schema::dropIfExists('clearances');
    }
};

