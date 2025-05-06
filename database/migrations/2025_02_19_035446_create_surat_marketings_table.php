<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratMarketingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_marketing', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('jenis_surat'); // Jenis surat
            $table->string('divisi_pembuat'); // Divisi pembuat surat
            $table->string('divisi_tujuan'); // Divisi tujuan surat
            $table->string('file_path')->default('uploads/default.pdf'); // Default file path
            $table->string('status_pengajuan')->default('Pending'); // Status pengajuan default 'Pending'
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surat_marketing');
    }
}
