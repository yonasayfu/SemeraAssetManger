<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'address_2')) {
                $table->string('address_2', 255)->nullable()->after('address');
            }
            if (!Schema::hasColumn('companies', 'brand_color')) {
                $table->string('brand_color', 7)->nullable()->after('logo');
            }
            if (!Schema::hasColumn('companies', 'secondary_color')) {
                $table->string('secondary_color', 7)->nullable()->after('brand_color');
            }
            if (!Schema::hasColumn('companies', 'brand_logo_height')) {
                $table->unsignedSmallInteger('brand_logo_height')->nullable()->after('secondary_color');
            }
            if (!Schema::hasColumn('companies', 'brand_title_size')) {
                $table->unsignedSmallInteger('brand_title_size')->nullable()->after('brand_logo_height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_title_size')) {
                $table->dropColumn('brand_title_size');
            }
            if (Schema::hasColumn('companies', 'brand_logo_height')) {
                $table->dropColumn('brand_logo_height');
            }
            if (Schema::hasColumn('companies', 'secondary_color')) {
                $table->dropColumn('secondary_color');
            }
            if (Schema::hasColumn('companies', 'brand_color')) {
                $table->dropColumn('brand_color');
            }
            if (Schema::hasColumn('companies', 'address_2')) {
                $table->dropColumn('address_2');
            }
        });
    }
};

