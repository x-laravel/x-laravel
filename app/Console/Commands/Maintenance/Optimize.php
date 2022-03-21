<?php

namespace App\Console\Commands\Maintenance;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class Optimize extends Command
{
    protected $signature = 'maintenance:optimize';
    protected $description = 'Sistem önyükleme dosyasını oluşturur ve veritabanını en iyi hale getirir.';

    public function handle(): void
    {
        $this->line('Optimizasyon başlatılıyor...');

        $this->line('Sistem önyükleme dosyalarını önbelleğe alınıyor...');
        $this->call('config:cache');

        $this->line('Veritabanı en iyi hale gelitiriliyor...');
        $this->systemDatabasesOptimize();
        $this->tenantDatabasesOptimize();

        $this->info('Optimizasyon tamamlandı!');
    }

    protected function systemDatabasesOptimize(): void
    {
        $this->line('Sistem veritabanları en iyi hale getiriliyor...');

        $databases = collect(config('database.connections'))->filter(function ($value) {
            return $value['driver'] === 'mysql';
        })->toArray();

        $progress = $this->output->createProgressBar(count($databases));
        foreach ($databases as $connectionName => $database) {
            $progress->advance();
            $this->databaseOptimize(DB::connection($connectionName), $database['database']);
        }

        $this->newLine();
    }

    protected function tenantDatabasesOptimize(): void
    {
        $this->line('Tenant veritabanları en iyi hale getiriliyor...');
        $progress = $this->output->createProgressBar(config('tenancy.tenant_model')::count());
        foreach (config('tenancy.tenant_model')::get() as $tenant) {
            $progress->advance();
            tenancy()->initialize($tenant);
            $this->databaseOptimize(DB::connection('tenant'), $tenant->tenancy_db_name);
        }

        $this->newLine();
    }

    protected function databaseOptimize(ConnectionInterface $connection, $databaseName): void
    {
        foreach ($connection->select('SHOW TABLES') as $table) {
            $tableName = $table->{'Tables_in_' . $databaseName};
            $connection->select('OPTIMIZE TABLE `' . $tableName . '`');
        }
    }
}
