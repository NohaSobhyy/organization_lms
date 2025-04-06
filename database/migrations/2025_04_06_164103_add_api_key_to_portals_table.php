<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add api_key column
        if (!Schema::hasColumn('portals', 'api_key')) {
            Schema::table('portals', function (Blueprint $table) {
                $table->string('api_key')->nullable()->after('bussiness_name');
            });

            // Generate API keys for existing portals
            $portals = DB::table('portals')->get();
            foreach ($portals as $portal) {
                $apiKey = Str::random(32);
                DB::table('portals')
                    ->where('id', $portal->id)
                    ->update(['api_key' => $apiKey]);

                // Log the generated API key for each portal
                Log::info('Generated API key for portal', [
                    'portal_id' => $portal->id,
                    'business_name' => $portal->bussiness_name,
                    'api_key' => $apiKey
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('portals', 'api_key')) {
            Schema::table('portals', function (Blueprint $table) {
                $table->dropColumn('api_key');
            });
        }
    }
};