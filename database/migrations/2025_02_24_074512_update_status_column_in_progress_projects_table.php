<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::table('progress_projects', function (Blueprint $table) {
            // Ubah kolom 'status' menjadi VARCHAR
            $table->string('status')->change();  // Mengganti tipe kolom 'status' menjadi VARCHAR
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        // Tidak perlu mengubah kolom kembali karena kita tidak ingin rollback ke enum
    }
};
