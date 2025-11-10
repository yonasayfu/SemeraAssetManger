<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_import_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->string('name');
            $table->jsonb('mapping');
            $table->jsonb('options')->nullable();
            $table->timestamps();
            $table->unique(['staff_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_import_presets');
    }
};

