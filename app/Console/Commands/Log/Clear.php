<?php

namespace App\Console\Commands\Log;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Clear extends Command
{
    protected $signature = 'log:clear {--keep-last : Son günlük dosyasının saklanıp saklanmayacağını belirtir}';
    protected $description = 'Günlük dizinindeki günlük dosyalarını kaldırır.';

    private Filesystem $disk;

    public function handle(): void
    {
        $this->disk = new Filesystem();
        $files = $this->getLogFiles();

        if ($this->option('keep-last') && $files->count() >= 1) {
            $files->shift();
        }

        $deleted = $this->delete($files);

        if (!$deleted) {
            $this->info('Günlük klasöründe silinecek günlük dosyası bulanamadı!');
        } else {
            $this->info($deleted . ' adet günlük dosyası silindi.');
        }
    }

    private function getLogFiles(): Collection
    {
        return Collection::make(
            $this->disk->allFiles(storage_path('logs'))
        )->sortBy('mtime');
    }

    private function delete(Collection $files): int
    {
        return $files->each(function ($file) {
            $this->disk->delete($file);
        })->count();
    }
}
