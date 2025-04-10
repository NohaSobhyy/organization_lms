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
        Schema::table('portals', function (Blueprint $table) {
            // Add the 'plan_id' column after the 'user_id' column
            $table->unsignedBigInteger('plan_id')->after('user_id');
            
            // Define the foreign key constraint, referencing the 'plans' table
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portals', function (Blueprint $table) {
            // Drop the foreign key constraint and the 'plan_id' column
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }
};
