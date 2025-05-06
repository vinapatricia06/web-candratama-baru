<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users1', function (Blueprint $table) {
            $table->string('role')->change(); // Mengubah kolom role menjadi VARCHAR
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users1', function (Blueprint $table) {
            $table->enum('role', ['teknisi', 'superadmin', 'admin', 'marketing', 'interior_consultan', 'warehouse', 'finance', 'project_production'])->change(); // Mengembalikan ke ENUM jika rollback
        });
    }
};
