<?php

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
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->text('filter');
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('filters')->insert(
            array(
                'description' => 'Dev. Sistemas Internos',
                'filter' => 'project in (BOT, CP, NF, AFL, BAC, FAT, IV, INTRA, UN)',
                'active' => true
            )
        );
        DB::table('filters')->insert(
            array(
                'description' => 'Dev. CloudVPS',
                'filter' => 'project in (MAU, CLOUD, GPRD, VPS, REV, WAF)',
                'active' => true
            )
        );
        DB::table('filters')->insert(
            array(
                'description' => 'Dev. EMails',
                'filter' => 'project in (KMAIL, IP, ECE, EPRO, EM, KINGCX, SMTPTSL)',
                'active' => false
            )
        );
        DB::table('filters')->insert(
            array(
                'description' => 'Dev. Canais',
                'filter' => 'project in (PN, PAIN, CX, CK, SITE, TDC, DSK) OR (project = Cart AND "Time responsável[Dropdown]" not in ( "Produtos - Integrações", Infraestrutura))',
                'active' => false
            )
        );
        DB::table('filters')->insert(
            array(
                'description' => 'Dev. Integrações',
                'filter' => 'project in (SHARED, REG, SSL, CD, HESP, HADDONS, "INT", REC, SEO, TE) OR (project = Cart AND "Time responsável[Dropdown]" = "Produtos - Integrações")',
                'active' => false
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filters');
    }
};
