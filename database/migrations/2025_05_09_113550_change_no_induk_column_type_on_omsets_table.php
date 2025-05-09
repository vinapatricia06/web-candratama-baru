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
        Schema::table('omsets', function (Blueprint $table) {
            // Mengubah tipe kolom 'no_induk' menjadi string (VARCHAR)
            $table->string('no_induk')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('omsets', function (Blueprint $table) {
            // Jika migrasi dibatalkan, kembalikan tipe kolom 'no_induk' menjadi integer
            $table->integer('no_induk')->change();
        });
    }
};
