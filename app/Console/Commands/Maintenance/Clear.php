<?php

namespace App\Console\Commands\Maintenance;

use Illuminate\Console\Command;

class Clear extends Command
{
    protected $signature = 'maintenance:clear';
    protected $description = 'Sistem önbelleğini temizler.';

    public function handle(): void
    {
        $this->line('Temizlik modu başlatılıyor...');

        if (config('lada-cache.active')) {
            $this->line('Lada Cache belleği temizleniyor...');
            $this->call('lada-cache:flush');
        }

        $this->line('Önbelleğe alınmış önyükleme dosyası temizleniyor...');
        $this->call('optimize:clear');

        $this->info('Önbellek başarıyla temizlendi!');
    }
}
