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
        Schema::create('surat_purchasing', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_surat');
            $table->string('divisi_pembuat');
            $table->string('divisi_tujuan');
            $table->string('file_path')->nullable();
            $table->string('status_pengajuan')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_purchasing');
    }
};
