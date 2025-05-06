<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoIndukToOmsetsTable extends Migration
{
    public function up()
    {
        Schema::table('omsets', function (Blueprint $table) {
            // Add no_induk column after 'tanggal'
            $table->integer('no_induk')->after('tanggal');
        });
    }

    public function down()
    {
        Schema::table('omsets', function (Blueprint $table) {
            // Rollback the addition of no_induk column
            $table->dropColumn('no_induk');
        });
    }
}
