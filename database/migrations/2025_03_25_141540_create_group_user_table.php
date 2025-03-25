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
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_user_id');
            $table->unsignedBigInteger('group_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('portal_user_id')->references('id')->on('portal_users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('portal_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_user');
    }
};
