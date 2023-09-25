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
        DB::statement(
            "CREATE OR REPLACE VIEW personas_list AS
                select ipcr.issue_id,
                       group_concat(pcr.displayName SEPARATOR ', ') as names
                from issues_personas ipcr
                left join personas pcr on (pcr.id = ipcr.persona_id)
                where ipcr.type = 'coresp'
                group by ipcr.issue_id;"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
