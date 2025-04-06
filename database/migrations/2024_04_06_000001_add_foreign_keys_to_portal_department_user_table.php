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
        Schema::table('portal_department_user', function (Blueprint $table) {
            // Drop existing foreign keys if they exist
            $table->dropForeign(['department_id']);
            $table->dropForeign(['user_id']);

            // Add foreign key constraints
            $table->foreign('department_id')
                  ->references('id')
                  ->on('portal_departments')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Add unique constraint if it doesn't exist
            if (!Schema::hasTable('portal_department_user')) {
                $table->unique(['department_id', 'user_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_department_user', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['user_id']);
        });
    }
}; 