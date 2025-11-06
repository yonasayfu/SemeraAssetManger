<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'purchase_order_item_id')) {
                $table->foreignId('purchase_order_item_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('purchase_order_items')
                    ->nullOnDelete();
            }
            $table->index(['purchase_order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'purchase_order_item_id')) {
                $table->dropConstrainedForeignId('purchase_order_item_id');
                $table->dropIndex(['purchase_order_item_id']);
            }
        });
    }
};

