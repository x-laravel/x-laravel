<?php

namespace App\Console\Commands\Maintenance;

use Illuminate\Console\Command;

class Backup extends Command
{
    protected $signature = 'maintenance:backup';
    protected $description = 'Veritabanı ve dosya yedekleme işlemini başlatır.';

    public function handle(): void
    {
        $this->line('Veritabanı ve dosya yedekleme işlemi başlatılıyor...');

        $this->line('Temizlik yapılıyor...');
        $this->call('backup:clean');

        $this->line('Yedekleniyor...');
        $this->call('backup:run');

        $this->info('Yedekleme işlemi başarıyla tamamlandı!');
    }
}
