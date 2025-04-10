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
        Schema::create('portal_plans', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->enum('type', ['Basic', 'Premium', 'customize']); 
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('portal_plans');
    }
};
