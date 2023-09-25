<?php

use App\Enums\PersonaTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Reference\Reference;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('issues_personas', function (Blueprint $table) {
            $enumsArr = PersonaTypeEnum::cases();
            $values = array_column($enumsArr, 'value');

            $table->id();
            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('persona_id');
            $table->enum('type', $values);
            $table->timestamps();

            $table->foreign('issue_id')->references('id')->on('issues');
            $table->foreign('persona_id')->references('id')->on('personas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues_personas');
    }
};
