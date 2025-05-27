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
        Schema::table('progress_projects', function (Blueprint $table) {
            $table->foreign('teknisi_id')->references('id_user')->on('users1')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('klien_id')->references('id')->on('kliens')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('omsets', function (Blueprint $table) {
            $table->foreign('progress_projects_id')->references('id')->on('progress_projects')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreign('progress_projects_id')->references('id')->on('progress_projects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_projects', function (Blueprint $table) {
            $table->dropForeign(['teknisi_id']);
            $table->dropForeign(['klien_id']);
        });

        Schema::table('omsets', function (Blueprint $table) {
            $table->dropForeign(['progress_projects_id']);
        });

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['progress_projects_id']);
        });
    }
};
