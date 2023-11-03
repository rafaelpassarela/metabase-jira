<?php

namespace App\Console\Commands;

use App\Http\Controllers\IssuesController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportIssues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-issues {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Jira Issues into Metabase using {date} as Updated date for Issues with resolution set.';

    /**
     * Execute the console command.
     */
    public function handle(IssuesController $controller): void
    {
        $date = $this->option('date');
        $valid = strtotime($date);
        if (!$valid) {
            $date = Carbon::yesterday()->format('Y-m-d');
        }

        $controller->setDate($date);
        $controller->setConsole(true);
        $controller->ImportIssues();
    }
}
