<?php

namespace SyncHub\LaravelSyncHub\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class PublishRoutesCommand extends Command
{
    protected $signature = 'synchub:install-routes
                            {--force : Sobrescreve arquivos existentes}';

    protected $description = 'Publica os arquivos de rotas do Laravel SyncHub';

    public function handle(): int
    {
        $packageRoot = dirname(__DIR__, 3);
        $routesDirectory = base_path('routes');

        $files = [
            'api.php' => 'synchub-api.php',
            'web.php' => 'synchub-web.php',
        ];

        File::ensureDirectoryExists($routesDirectory);

        foreach ($files as $source => $destination) {
            $sourcePath = $packageRoot . '/routes/' . $source;
            $destinationPath = $routesDirectory . '/' . $destination;

            if (! File::exists($sourcePath)) {
                throw new RuntimeException(
                    "Arquivo de rotas do pacote não encontrado: {$sourcePath}"
                );
            }

            if (
                File::exists($destinationPath)
                && ! $this->option('force')
            ) {
                $this->warn(
                    "Arquivo preservado: routes/{$destination}"
                );

                continue;
            }

            File::copy($sourcePath, $destinationPath);

            $this->info(
                "Arquivo publicado: routes/{$destination}"
            );
        }

        $this->newLine();

        $this->line(
            'Revise os arquivos publicados antes de habilitar sua utilização.'
        );

        $this->line(
            'Configure synchub.routes.api.source e synchub.routes.web.source como application.'
        );

        return self::SUCCESS;
    }
}
