<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('project_code')->constrained('vendors')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('vendor_id')->constrained('products')->nullOnDelete();
            }
            $table->index(['vendor_id']);
            $table->index(['product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
                $table->dropIndex(['product_id']);
            }
            if (Schema::hasColumn('assets', 'vendor_id')) {
                $table->dropConstrainedForeignId('vendor_id');
                $table->dropIndex(['vendor_id']);
            }
        });
    }
};

