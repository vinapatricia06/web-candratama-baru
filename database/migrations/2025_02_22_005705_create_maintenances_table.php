<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klien');
            $table->text('alamat');
            $table->string('project');
            $table->date('tanggal_setting');
            $table->string('no_induk')->unique(); 
            $table->date('tanggal_serah_terima')->nullable();
            $table->string('maintenance');
            $table->string('dokumentasi')->nullable();
            $table->enum('status', ['Waiting List', 'Selesai'])->default('Waiting List');
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down() {
        Schema::dropIfExists('maintenances');
    }
};
