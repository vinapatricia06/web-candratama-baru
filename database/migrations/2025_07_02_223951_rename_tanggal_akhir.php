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
            $table->renameColumn('tanggal_akhir', 'tanggal_selesai');
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->renameColumn('tanggal_akhir', 'tanggal_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_projects', function (Blueprint $table) {
            $table->renameColumn('tanggal_selesai', 'tanggal_akhir');
        });
        Schema::table('maintenances', function (Blueprint $table) {
            $table->renameColumn('tanggal_selesai', 'tanggal_akhir');
        });
    }
};
