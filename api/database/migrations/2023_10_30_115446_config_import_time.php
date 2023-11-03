<?php

use App\Models\Config;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $config = new Config(array(
            'description' => 'Last Auto Import Time',
            'value' => '00:00:00'
        ));
        $config->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
