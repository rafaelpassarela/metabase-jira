<?php

use App\Models\Config;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('description', 50)->nullable(false);
            $table->string('value', 255)->nullable(false);
            $table->timestamps();
        });

        // Insert some stuff
        $config = new Config(array(
            'description' => 'Last Auto Import Date',
            'value' => ''
        ));
        $config->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
