<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNominalColumnInOmsetsTable extends Migration
{
    /**
     * Menjalankan migrasi untuk mengubah tipe data kolom nominal menjadi DECIMAL(20,2).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('omsets', function (Blueprint $table) {
            // Mengubah kolom nominal menjadi DECIMAL(20,2)
            $table->decimal('nominal', 20, 2)->change();
        });
    }

    /**
     * Membatalkan perubahan tipe data kolom nominal jika rollback.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('omsets', function (Blueprint $table) {
            // Mengembalikan ke tipe lama DECIMAL(10,2) jika rollback
            $table->decimal('nominal', 15, 2)->change();
        });
    }
}
