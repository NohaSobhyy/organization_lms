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
        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bussiness_name')->unique();
            $table->string('meeting_time')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->unique();
            $table->string('address');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('url_name')->unique()->nullable();
            $table->integer('max_users')->nullable();
            $table->string('zoom_meeting_id')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('other_link')->nullable();
            $table->boolean('independent_copyright')->default(false);
            $table->boolean('accepted')->default(false);
            $table->boolean('activated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portals');
    }
};
