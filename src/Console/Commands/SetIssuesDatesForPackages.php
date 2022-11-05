<?php


namespace MyriadDataStore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetIssuesDatesForPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'myriad-download:set-issues-dates-for-packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';


    public function handle(): int
    {
        $tableOrderPackages = config('myriad-data-store.tables.order_packages');
        $tableIssues        = config('myriad-data-store.tables.issues');
        DB::statement("
            UPDATE {$tableOrderPackages}
            LEFT JOIN {$tableIssues} ON
                        {$tableOrderPackages}.title_id = {$tableIssues}.title_id
                         AND {$tableOrderPackages}.start_issue = {$tableIssues}.name
            SET {$tableOrderPackages}.start_issue_dn = {$tableIssues}.publication_date
            WHERE true;
        ");

        DB::statement("
            UPDATE {$tableOrderPackages}
            LEFT JOIN {$tableIssues} ON
                        {$tableOrderPackages}.title_id = {$tableIssues}.title_id
                         AND {$tableOrderPackages}.end_issue = {$tableIssues}.name
            LEFT JOIN {$tableIssues} as mi ON
                        {$tableOrderPackages}.title_id = mi.title_id
                         AND {$tableOrderPackages}.start_issue = mi.name
            SET {$tableOrderPackages}.end_issue_dn = IF({$tableIssues}.publication_date IS NULL, mi.publication_date, {$tableIssues}.publication_date)
            WHERE true;
        ");

        return 0;
    }
}
