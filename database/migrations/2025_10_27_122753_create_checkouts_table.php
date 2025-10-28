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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('assignee_type'); // person|department|customer
            $table->unsignedBigInteger('assignee_id');
            $table->date('due_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('condition_out_id')->nullable();
            $table->string('condition_in_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed', 'overdue'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
