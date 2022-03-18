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
        $this->call('optimize');

        $this->line('Veritabanı en iyi hale gelitiriliyor...');
        $this->systemDatabasesOptimize();

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

    protected function databaseOptimize(ConnectionInterface $connection, $databaseName): void
    {
        foreach ($connection->select('SHOW TABLES') as $table) {
            $tableName = $table->{'Tables_in_' . $databaseName};
            $connection->select('OPTIMIZE TABLE `' . $tableName . '`');
        }
    }
}
