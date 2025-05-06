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
        Schema::create('users1', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['teknisi', 'superadmin', 'admin', 'marketing', 'interior_consultan', 'warehouse', 'finance', 'project_production']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users1');
    }
};
