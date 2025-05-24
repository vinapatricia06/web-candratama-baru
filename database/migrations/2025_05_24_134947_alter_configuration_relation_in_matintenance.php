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
        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('progress_projects_id')->nullable()->after('id');
            $table->dropColumn('nama_klien');
            $table->dropColumn('alamat');
            $table->dropColumn('project');
            $table->dropColumn('no_induk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn('progress_projects_id');
            $table->string('nama_klien');
            $table->text('alamat');
            $table->string('project');
            $table->string('no_induk')->unique();
        });
    }
};
