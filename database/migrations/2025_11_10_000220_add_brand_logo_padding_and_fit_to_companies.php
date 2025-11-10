<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'brand_logo_padding')) {
                $table->unsignedSmallInteger('brand_logo_padding')->nullable()->after('brand_logo_height');
            }
            if (!Schema::hasColumn('companies', 'brand_logo_fit')) {
                $table->string('brand_logo_fit', 16)->nullable()->after('brand_logo_padding');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_logo_fit')) {
                $table->dropColumn('brand_logo_fit');
            }
            if (Schema::hasColumn('companies', 'brand_logo_padding')) {
                $table->dropColumn('brand_logo_padding');
            }
        });
    }
};

