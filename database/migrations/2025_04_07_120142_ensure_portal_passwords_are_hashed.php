<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Portal;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Find all portals with non-hashed passwords
        $portals = Portal::whereRaw("LENGTH(password) < 60")->get();
        
        foreach ($portals as $portal) {
            if ($portal->password) {
                // Hash the password
                $portal->password = Hash::make($portal->password);
                $portal->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is not reversible
    }
};
