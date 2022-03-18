<?php

namespace App\Console\Commands;

use App\Notifications\DeployNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Process\Process;

class Deploy extends Command
{
    protected $signature = 'deploy {--branchName=}';
    protected $description = 'Otomatik deploy işlemini yapar.';

    private ?string $gitPullOutput = null;

    public function handle()
    {
        $this->sendLog('Deploy işlemi başlatıldı!');

        $this->call('optimize:clear');

        $this->maintenanceOpen();
        $this->gitPull();

        $this->composerUpdate();
        $this->appOptimize();
        $this->migrate();

        $this->npmUpdate();
        $this->assetsBuild();

        $this->queueRestart();
        $this->maintenanceClose();

        $this->sendLog('Deploy işlemi tamamlandı!');
    }

    private function sendLog(string $message, bool $isPushMessage = true): void
    {
        $this->line($message);

        Log::channel('deploy')->info($message);

        if ($isPushMessage) {
            Notification::route('slack', config('logging.channels.slack.url'))
                ->notify(new DeployNotification($message));
        }
    }

    private function maintenanceOpen(): void
    {
        $this->sendLog('Sistem bakım moduna alınıyor...');
        $this->call('down');
    }

    private function gitPull(): void
    {
        $this->sendLog('Güncellemeler alınıyor...');
        if ($this->option('branchName')) {
            $this->command('git checkout -b ' . $this->option('branchName') . ' origin/' . $this->option('branchName'));

            $this->command('git checkout ' . $this->option('branchName'));
        }
        $this->gitPullOutput = $this->command('git pull');

        $this->command('git status');
    }

    private function command(string $command): string
    {
        $this->sendLog('Command: ' . $command, false);

        $process = new Process(explode(' ', $command), base_path());
        $process->setTimeout(null)->run();
        $output = $process->getOutput();

        $this->sendLog('Result: ' . "\n" . $output, false);
        return $output;
    }

    private function composerUpdate(): void
    {
        preg_match_all('/(composer.json )/i', $this->gitPullOutput, $matches);
        if (count($matches[0])) {
            $this->sendLog('Composer paketleri güncelleniyor...');
            $this->command('composer u --no-ansi --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader' . (!App::environment('production') ? ' --no-dev' : null));
        }
    }

    private function appOptimize(): void
    {
        $this->sendLog('Uygulama yapılandırması en iyi hale getiriliyor...');
        $this->call('optimize');
        $this->call('cache:clear');
        $this->call('lada-cache:flush');
    }

    private function migrate(): void
    {
        $this->sendLog('Migrate işlemi başlatılıyor...');
        $this->call('migrate', [
            '--force' => true,
        ]);
    }

    private function npmUpdate(): void
    {
        preg_match_all('/(package.json )/i', $this->gitPullOutput, $matches);
        if (count($matches[0])) {
            $this->sendLog('Npm paketleri güncelleniyor...');
            $this->command('npm install --no-ansi --no-interaction --no-progress --scripts-prepend-node-path=auto');
        }
    }

    private function assetsBuild(): void
    {
        preg_match_all('/(\.js )|(\.css )|(\.scss )/i', $this->gitPullOutput, $matches);
        if (count($matches[0])) {
            $this->sendLog('CSS ve JS dosyaları derleniyor...');
            $this->command('npm run production --scripts-prepend-node-path=auto');
        }
    }

    private function queueRestart(): void
    {
        preg_match_all('/(\.php )/i', $this->gitPullOutput, $matches);
        if (count($matches[0])) {
            $this->sendLog('Kuyruk işleri yeniden başlatılıyor...');
            $this->call('queue:restart');
        }
    }

    private function maintenanceClose(): void
    {
        $this->sendLog('Sistem bakım modundan çıkarılıyor...');
        $this->call('up');
    }
}
