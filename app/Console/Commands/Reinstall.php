<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use MongoDB\Client as MongoClient;

class Reinstall extends Command
{
    protected $signature = 'reinstall';
    protected $description = 'Otomatik yeniden kurulum yapar.';

    private array $databases = [];


    public function handle(): void
    {
        if (App::environment('production')) {
            $this->error('Yeniden kurulum işlemi canlı ortamda başlatılamaz!');
            return;
        }

        $this->line('Yeniden kurulum işlemi başlatıldı!');
        $this->newLine();

        $this->maintenanceOpen();

        $this->clearLogFiles();
        $this->clearStoreFiles();
        $this->clearBackupFiles();

        $this->removeMongodbDatabases();

        $this->removeMysqlDatabases();
        $this->installMysqlDatabases();
        $this->migration();

        $this->queueRestart();

        $this->maintenanceClose();
    }

    private function maintenanceOpen(): void
    {
        $this->line('Sistem bakım moduna alınıyor...');
        $this->call('down');

        $this->newLine();
    }


    private function clearLogFiles(): void
    {
        $this->line('Log dosyaları temizleniyor...');
        $this->call('log:clear');

        $this->newLine();
    }

    private function clearStoreFiles(): void
    {
        $this->line('Uygulama depolama alanı temizleniyor...');

        $disk = new Filesystem();
        $deleted = collect($disk->directories(config('filesystems.disks.public.root')))->each(function ($item) use ($disk) {
            $disk->deleteDirectory($item);
        })->count();
        $this->info($deleted . ' klasör silindi.');

        $disk = new Filesystem();
        $deleted = collect($disk->allFiles(config('filesystems.disks.local.root')))->each(function ($item) use ($disk) {
            $disk->delete($item);
        })->count();
        $this->info($deleted . ' dosya silindi.');

        $this->newLine();
    }

    private function clearBackupFiles(): void
    {
        $this->line('Uygulama yedek alanı temizleniyor...');

        $deleted = collect(Storage::disk('backup-local')->allDirectories())->each(function ($item) {
            Storage::disk('backup-local')->deleteDirectory($item);
        })->count();
        $this->info($deleted . ' klasör silindi.');

        $this->newLine();
    }


    private function getMongodbClient(array $database): MongoClient
    {
        return new MongoClient(sprintf('mongodb://%s:%d', $database['host'], $database['port']), [
            'username' => $database['username'],
            'password' => $database['password'],
        ]);
    }

    private function removeMongodbDatabases(): void
    {
        $this->line('MongoDB veritabanları kaldırılıyor...');

        $this->databases['mongodb'] = collect(config('database.connections'))->filter(function ($value) {
            return $value['driver'] === 'mongodb';
        })->toArray();

        foreach ($this->databases['mongodb'] as $database) {
            $this->warn($database['database']);
            $client = $this->getMongodbClient($database);
            $client->dropDatabase($database['database']);
        }

        $this->newLine();
    }


    private function getMysqlClient(array $database): \PDO
    {
        return new \PDO(sprintf('mysql:host=%s;port=%d;', $database['host'], $database['port']), $database['username'], $database['password']);
    }

    private function removeMysqlDatabases(): void
    {
        $this->line('MySQL veritabanları kaldırılıyor...');

        $this->databases = collect(config('database.connections'))->filter(function ($value) {
            return $value['driver'] === 'mysql';
        })->toArray();

        foreach ($this->databases as $database) {
            $this->warn($database['database']);
            $pdo = $this->getMysqlClient($database);
            $pdo->exec('SET foreign_key_checks = 0');
            $pdo->exec('DROP DATABASE IF EXISTS `' . $database['database'] . '`');
        }

        $this->newLine();
    }

    private function installMysqlDatabases(): void
    {
        $this->line('MySQL veritabanları yeniden oluşturuluyor...');

        foreach ($this->databases as $database) {
            $pdo = $this->getMysqlClient($database);
            try {
                $pdo->exec('CREATE DATABASE `' . $database['database'] . '` CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;');

                $this->info($database['database']);
            } catch (\PDOException $exception) {
                $this->error(sprintf('Failed to create %s database, %s', $database['database'], $exception->getMessage()));
            }
        }

        $this->newLine();
    }


    private function migration(): void
    {
        $this->line('Migrate işlemi başlatılıyor...');

        $this->call('migrate');
        $this->call('db:seed');

        $this->newLine();
    }


    private function queueRestart(): void
    {
        $this->line('Kuyruk işleri yeniden başlatılıyor...');
        $this->call('queue:restart');

        $this->newLine();
    }


    private function maintenanceClose(): void
    {
        $this->line('Sistem bakım modundan çıkarılıyor...');
        $this->call('up');
    }
}
