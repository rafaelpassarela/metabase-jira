<?php

namespace App\Console\Commands;

use App\Http\Controllers\IssuesController;
use App\Models\Config;
use Illuminate\Console\Command;

class AutoImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-import {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Jira Issues into Metabase using {date} as Updated date for Issues with resolution set from the last imported date, or the givem date.';

    /**
     * Execute the console command.
     */
    public function handle(IssuesController $controller): void
    {
        $date = $this->option('date');
        $valid = strtotime($date);
        if (!$valid) {
            $config = Config::find(1);
            $date = $config->value;
        }

        echo "Checking from $date \n";

        $format = 'Y-m-d';
        // $your_date = strtotime("1 day", strtotime("2016-08-24"));
        // $new_date = date("Y-m-d", $your_date);

        $maxDate = date($format,strtotime("-1 days"));
        while ($date <= $maxDate) {
            $controller->setDate($date);
            $controller->setConsole(true);
            $controller->ImportIssues();

            $date = date($format, strtotime("1 day", strtotime($date)));
        }
    }
}
