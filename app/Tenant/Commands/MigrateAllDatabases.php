<?php

namespace App\Tenant\Commands;

use Illuminate\Console\Command;
use Artisan;
use DB;

class MigrateAllDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrating all databases..';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // GET ALL TENANT NAMES
        $tenants = DB::table('tenants')->get();
        foreach($tenants as $tenant) {
            $current_tenant = 'tke_'.$tenant->name;
            config()->set('database.connections.tenant.database', $current_tenant);
            DB::purge('tenant');
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--seed' => true,
            ]);

            $this->info("Tenant $tenant->name migrate finished...");
        }
        $this->info('All databases are migrated and seeded.');
    }
}
