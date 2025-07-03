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
        Schema::create('debt_payment_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_projects_id')->nullable();
            $table->date('tanggal_angsuran');
            $table->integer('nominal');
            $table->string('kode_transaksi')->nullable();
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('status_pembayaran')->default('Belum Dibayar');
            $table->text('snap_token')->nullable();
            $table->timestamps();

            $table->foreign('progress_projects_id')->references('id')->on('progress_projects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payment_projects');
    }
};
