<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'brand_logo_offset_x')) {
                $table->smallInteger('brand_logo_offset_x')->nullable()->after('brand_logo_scale');
            }
            if (!Schema::hasColumn('companies', 'brand_logo_offset_y')) {
                $table->smallInteger('brand_logo_offset_y')->nullable()->after('brand_logo_offset_x');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_logo_offset_y')) {
                $table->dropColumn('brand_logo_offset_y');
            }
            if (Schema::hasColumn('companies', 'brand_logo_offset_x')) {
                $table->dropColumn('brand_logo_offset_x');
            }
        });
    }
};

