<?php

namespace App\Console\Commands\Maintenance;

use Illuminate\Console\Command;

class Run extends Command
{
    protected $signature = 'maintenance:run';
    protected $description = 'Sistem bakımı ve yedekleme süreçlerini yönetir.';

    public function handle(): void
    {
        $this->line('Sistem bakımı çalıştırılıyor...');
        $this->newLine();

        $this->before();
        $this->start();
        $this->after();

        $this->info('Sistem bakımı tamamlandı!');
        $this->newLine();
    }

    private function before(): void
    {
        $this->line('Sistem bakım moduna alınıyor...');
        $this->call('down');
    }

    private function start(): void
    {
        $this->call('maintenance:clear');
        $this->newLine();

        $this->call('maintenance:backup');
        $this->newLine();

        $this->call('maintenance:optimize');
        $this->newLine();
    }

    private function after(): void
    {
        $this->line('Sistem bakım modundan çıkarılıyor...');
        $this->call('up');
    }
}
