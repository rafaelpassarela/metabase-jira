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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id', false, true)->nullable(false);
            $table->string('keyJira', 20);
            $table->text('summary');
            $table->integer('storyPoints')->default(0);
            $table->string('issueType')->nullable(false);
            $table->string('classe', 50)->nullable();
            $table->string('tema', 50)->nullable();
            $table->string('subTema', 50)->nullable();
            $table->string('areaDemandante', 50)->nullable();
            $table->bigInteger('sprintId', false, true)->nullable(true);
            $table->string('sprintName', 32)->nullable(true);
            $table->string('status', 50)->nullable(false);
            $table->string('priority', 50)->nullable();
            $table->string('parentKey', 20)->nullable();
            $table->string('parentUrl', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('resolution', 20)->nullable();
            $table->unsignedBigInteger('filterId')->nullable(false);
            $table->timestamp('resolvedAt')->nullable();
            $table->timestamp('lastUpdated')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('filterId')->references('id')->on('filters');
            $table->index('keyJira');
            $table->index('parentKey');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
