<?php

namespace App\Tenant\Commands;

use App\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Hash;
use DB;

class CreateNewTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {tenant_name} {tenant_domain} {tenant_password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating new tenant database...';

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
        // CREATE NEW DATABASE
        $tenant_name = $this->argument('tenant_name');
        $tenant_domain = $this->argument('tenant_domain');
        $tenant_password = $this->argument('tenant_password');

        $charset = config("database.connections.mysql.charset",'utf8');
        $collation = config("database.connections.mysql.collation",'utf8_unicode_ci');
        $query = "CREATE DATABASE tke_$tenant_name CHARACTER SET $charset COLLATE $collation;";
         try {
             DB::statement($query);
             // INSERT DATABASE NAME TO LANDLORD DATABASE TABLE `COMPANIES`
             $new_tenant_id = DB::table('tenants')->insertGetId([
                 'name' => $tenant_name,
                 'domain' => $tenant_domain,
             ]);
             $this->info('Tenant with the name `'.$tenant_name.'` was created successfully!');

             $current_tenant = 'tke_'.$tenant_name;

             // RUN MIGRATION AND SEED
             config()->set('database.connections.tenant.database', $current_tenant);
             Artisan::call('migrate', [
                 '--database' => 'tenant',
                 '--seed' => true,
             ]);
             $this->info('Tenant migration and seeding were created successfully!');

             // CREATE SUPER_ADMIN USER FOR TENANT
             config()->set('database.default', 'landlord');
             $user = User::create([
                 'tenant_id' => $new_tenant_id,
                 'name' => 'SUPER ADMIN',
                 'email' => 'admin@'.$tenant_domain,
                 'password' => Hash::make($tenant_password),
             ]);

             // ASSIGN ROLE TO THE CREATED TENANT SUPER ADMIN USER
             $user->assignRole('super_admin');
         } catch(\Exception $e) {
             $this->info($e);
         };
    }
}
