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
        Schema::create('surat_ekspedisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');  // Nama user yang login
            $table->string('divisi'); // Divisi, berdasarkan role
            $table->text('keperluan');  // Keperluan
            $table->string('file_path'); // Lokasi file PDF yang diupload
            $table->string('status_pengajuan')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_ekspedisi');
    }
};
