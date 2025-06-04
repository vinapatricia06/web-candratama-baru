<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->string('kode_transaksi')->nullable()->after('status');
            $table->string('status_pembayaran')->nullable()->after('kode_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
            $table->dropColumn('status_pembayaran');
        });
    }
};
