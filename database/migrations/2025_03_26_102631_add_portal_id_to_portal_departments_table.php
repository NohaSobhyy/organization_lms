<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('portal_id')->after('id');
            $table->foreign('portal_id')->references('id')->on('portals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_departments', function (Blueprint $table) {
            $table->dropForeign(['portal_id']);
            $table->dropColumn('portal_id');
        });
    }
};
