<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_import_jobs', function (Blueprint $table) {
            $table->boolean('cancelled')->default(false)->after('status');
            $table->index('cancelled');
        });
    }

    public function down(): void
    {
        Schema::table('asset_import_jobs', function (Blueprint $table) {
            $table->dropIndex(['cancelled']);
            $table->dropColumn('cancelled');
        });
    }
};

