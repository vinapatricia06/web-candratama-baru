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
        Schema::table('progress_projects', function (Blueprint $table) {
            $table->string('kode_transaksi')->nullable()->after('nominal');
            $table->text('snap_token')->nullable()->after('status_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_projects_id', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
            $table->dropColumn('snap_token');
        });
    }
};
