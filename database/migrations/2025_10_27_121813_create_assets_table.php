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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag', 50)->unique();
            $table->text('description');
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('purchased_from', 150)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_no', 150)->nullable();
            $table->string('project_code', 150)->nullable();
            $table->enum('asset_condition', ['New', 'Good', 'Fair', 'Poor', 'Broken'])->nullable();
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('set null');
            $table->enum('status', ['Available', 'Checked Out', 'Under Repair', 'Leased', 'Disposed', 'Lost', 'Donated', 'Sold']);
            $table->string('photo', 255)->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
