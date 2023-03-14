<?php

namespace DevMoez\AuthLastActivity\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use DevMoez\AuthLastActivity\Models\AuthLastActivity;
use Illuminate\Support\Facades\DB;

class InstallAuthLastActivityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth-last-activity:install {--drop-table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install AuthLastActivity package into your laravel application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $config_file = 'auth-last-activity.php';
        $migration_file = '2023_03_01_133235_create_auth_last_activities_table.php';
        $table = (new AuthLastActivity())->getTable();
        

        // Check if --drop-table option is passed
        if ($this->option('drop-table'))
        {
            DB::table('migrations')->where('migration', explode(".", $migration_file)[0])->delete();
            Schema::connection(config('auth-last-activity.connection'))->dropIfExists($table);
            $this->info("$table table is dropped");
        }

        // Publish config
        if (File::exists(config_path($config_file))) {
            if ($this->confirm("auth-last-activity.php config file already exist. Do you want to overwrite it?")) {
                $this->info("Overwriting config file...");
                $this->publishConfig();
                $this->info("auth-last-activity.php overwrite finished.");
            } else {
                $this->info("Skipped config file publish.");
            }
        } else {
            $this->publishConfig();
            $this->info("Config file published.");
        }

        // Publish migration
        if (File::exists(database_path("migrations/$migration_file"))) {
            if ($this->confirm("$migration_file migration file already exist. Do you want to overwrite it?")) {
                $this->info("Overwriting migration file...");
                $this->publishMigration();
                $this->info("$migration_file overwrite finished.");
            } else {
                $this->info("Skipped migration file publish.");
            }
        } else {
            $this->publishMigration();
            $this->info("Migration file published.");
        }

        // Run migrations
        if (!Schema::connection(config('auth-last-activity.connection'))->hasTable($table) ) {
            $this->call('migrate');
        }
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "DevMoez\AuthLastActivity\Services\AuthLastActivityServiceProvider",
            '--tag'      => 'auth-last-activity-config',
            '--force'    => true
        ]);
    }

    private function publishMigration()
    {
        $this->call('vendor:publish', [
            '--provider' => "DevMoez\AuthLastActivity\Services\AuthLastActivityServiceProvider",
            '--tag'      => 'auth-last-activity-migrations',
            '--force'    => true
        ]);
    }
}
