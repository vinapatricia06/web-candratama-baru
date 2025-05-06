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
            // Menambahkan kolom serah_terima dengan tipe enum
            $table->enum('serah_terima', ['selesai', 'belum'])->default('belum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_projects', function (Blueprint $table) {
            // Menghapus kolom serah_terima jika rollback
            $table->dropColumn('serah_terima');
        });
    }
};
