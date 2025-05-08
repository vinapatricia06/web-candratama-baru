<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('progress_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teknisi_id'); 
            $table->foreign('teknisi_id')->references('id_user')->on('users1')->onDelete('cascade'); 
            $table->string('nama_klien');
            $table->text('alamat');
            $table->string('project');
            $table->date('tanggal_setting');
            $table->string('dokumentasi')->nullable();
            $table->enum('status', ['Waiting List', 'Selesai'])->default('Waiting List');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('progress_projects');
    }
};
