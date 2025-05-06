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
        Schema::create('omsets', function (Blueprint $table) {
            $table->id('id_omset');
            $table->date('tanggal');
            $table->string('nama_klien');
            $table->text('alamat');
            $table->string('project');
            $table->string('sumber_lead');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omsets');
    }
};
