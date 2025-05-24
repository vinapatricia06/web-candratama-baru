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
            $table->unsignedBigInteger('klien_id')->nullable()->after('teknisi_id');
            $table->dropColumn('nama_klien');
            $table->dropColumn('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_projects', function (Blueprint $table) {
            $table->dropColumn('klien_id');
            $table->string('nama_klien');
            $table->text('alamat');
        });
    }
};
