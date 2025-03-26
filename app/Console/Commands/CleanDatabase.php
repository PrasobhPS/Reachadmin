<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all data from the database except for certain tables.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current', $tables);

        $preservedTables = ['currency_exchange_rates', 'master_settings', 'migrations', 'reachMembershipPage', 'reach_admin', 'reach_boat_location', 'reach_boat_type', 'reach_boats', 'reach_chandlery', 'reach_club_house', 'reach_countries', 'reach_current_availability', 'reach_email_templates', 'reach_experience', 'reach_home_page_cms', 'reach_job_duration', 'reach_job_roles', 'reach_languages', 'reach_members', 'reach_page_title', 'reach_partners', 'reach_positions', 'reach_qualifications', 'reach_referral_types', 'reach_salary_expectations', 'reach_site_pages', 'reach_stripe_accounts', 'reach_vessel_type', 'reach_visa'];  // Specify tables you want to keep
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            if (!in_array($table, $preservedTables)) {
                $this->info("Truncating table: $table");
                DB::table($table)->truncate();  // Remove all data from the table
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Database cleanup complete!');
    }
}
