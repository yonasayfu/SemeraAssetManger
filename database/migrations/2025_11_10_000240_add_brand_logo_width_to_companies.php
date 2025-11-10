<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'brand_logo_width')) {
                $table->unsignedSmallInteger('brand_logo_width')->nullable()->after('brand_logo_height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_logo_width')) {
                $table->dropColumn('brand_logo_width');
            }
        });
    }
};

