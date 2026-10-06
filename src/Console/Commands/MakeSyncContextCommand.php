<?php

namespace Synchub\LaravelSynchub\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;

class MakeSyncContextCommand extends Command
{
    protected $signature = '
        make:synchub-context
        {name : Nome do contexto}
        {--identifier= : Identificador usado para registrar o contexto no SyncHub}
        {--force : Sobrescreve arquivos existentes}
    ';


    protected $description = 'Cria um contexto de sincronização';


    public function handle(
        Filesystem $files
    ): int {

        $input = $this->argument('name');
        $identifier = $this->option('identifier');

        if (!$identifier) {
            $this->error(
                'O identificador do contexto é obrigatório.'
            );

            $this->line('');
            $this->line(
                'Exemplo:'
            );

            $this->line(
                '  php artisan make:synchub-context Customer --identifier=customers'
            );

            return self::FAILURE;
        }

        $segments = collect(
            explode('/', $input)
        )
        ->map(
            fn ($segment) => Str::studly($segment)
        );


        $className = $segments->last();


        $namespacePath = $segments
            ->slice(0, -1)
            ->implode('/');


        $directory = $namespacePath
            ? "Contexts/{$namespacePath}/{$className}"
            : "Contexts/{$className}";


        $path = app_path($directory);

        if (!$files->isDirectory($path)) {
            $files->makeDirectory(
                $path,
                0755,
                true
            );
        }

        $templates = [

            'SyncContext.php',
            'SyncMapper.php',
            'SyncValidator.php',
            'SyncDependencyChecker.php',
            'AfterSyncHandler.php',

            // gateways
            'SourceGateway.php',
            'TargetGateway.php',

            // identity
            'SourceIdentityResolver.php',
        ];



        foreach ($templates as $template) {


            $class = Str::replace(
                '.php',
                '',
                $template
            );


            $class = $className . $class;



            $file = "{$path}/{$class}.php";



            if (
                $files->exists($file)
                &&
                !$this->option('force')
            ) {

                $this->warn(
                    "{$class}.php já existe."
                );

                continue;
            }



            $content = $this->stub(
                $template,
                $className,
                $segments->implode('\\'),
                $identifier
            );



            $files->put(
                $file,
                $content
            );



            $this->info(
                "{$class}.php criado"
            );
        }

        $this->newLine();

        $this->info(
            "Contexto [{$identifier}] criado com sucesso."
        );

        $this->line('');

        $this->line(
            'Registre o contexto utilizando:'
        );

        $this->line('');

        $this->line(
            "Sync::register("
        );

        $this->line(
            "    '{$identifier}',"
        );

        $this->line(
            "    {$segments->implode('\\')}\\{$className}SyncContext::class,"
        );

        $this->line(
            "    {$segments->implode('\\')}\\{$className}SourceIdentityResolver::class,"
        );

        $this->line(
            ");"
        );

        return self::SUCCESS;
    }

    protected function stub(
        string $file,
        string $name,
        string $namespace,
        string $identifier
    ): string {
        $stub = __DIR__
            . '/../stubs/'
            . $file
            . '.stub';

        if (!file_exists($stub)) {
            throw new RuntimeException(
                "Stub não encontrado: {$stub}"
            );
        }

        return str_replace(
            [
                '{{ namespace }}',
                '{{ name }}',
                '{{ identifier }}',
            ],
            [
                "App\\Contexts\\{$namespace}",
                $name,
                $identifier,
            ],
            file_get_contents($stub)
        );
    }
}
