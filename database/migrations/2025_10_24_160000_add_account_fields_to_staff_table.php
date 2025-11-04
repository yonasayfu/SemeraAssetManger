<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('account_status', 20)
                ->default('pending')
                ->after('password')
                ->index();
            $table->string('account_type', 20)
                ->default('external')
                ->after('account_status')
                ->index();
            $table->timestamp('approved_at')->nullable()->after('account_type');
            $table->foreignId('approved_by')
                ->nullable()
                ->after('approved_at')
                ->constrained('staff')
                ->nullOnDelete();
        });

        DB::table('staff')->update([
            'account_status' => 'active',
            'account_type' => 'internal',
            'approved_at' => DB::raw("COALESCE(approved_at, CURRENT_TIMESTAMP)"),
        ]);
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approved_by',
                'approved_at',
                'account_type',
                'account_status',
            ]);
        });
    }
};
