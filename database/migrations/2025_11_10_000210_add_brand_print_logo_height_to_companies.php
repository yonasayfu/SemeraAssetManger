<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'brand_print_logo_height')) {
                $table->unsignedSmallInteger('brand_print_logo_height')->nullable()->after('brand_logo_height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'brand_print_logo_height')) {
                $table->dropColumn('brand_print_logo_height');
            }
        });
    }
};

