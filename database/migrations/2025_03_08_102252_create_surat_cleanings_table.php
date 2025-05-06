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
        Schema::create('surat_cleanings', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // User's name
            $table->string('divisi'); // Division of the user
            $table->string('keperluan'); // Purpose of the letter
            $table->string('file_path')->nullable(); // Path to the uploaded file (nullable)
            $table->enum('status_pengajuan', ['Pending', 'ACC', 'Tolak'])->default('Pending'); // Status of the request
            $table->timestamps(); // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_cleanings');
    }
};
