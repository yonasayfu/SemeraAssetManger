<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'brand_logo_scale')) {
                $table->unsignedSmallInteger('brand_logo_scale')->nullable()->after('brand_logo_padding');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_logo_scale')) {
                $table->dropColumn('brand_logo_scale');
            }
        });
    }
};

