<?php

namespace Synchub\LaravelSynchub\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;

class MakeSynchubCommand extends Command
{
    protected $signature = 'make:synchub
        {type : Tipo do componente (context, mapper, validator, dependency, after-sync, identity-resolver)}
        {name : Nome do componente}
        {--force : Sobrescreve arquivos existentes}';

    protected $description = 'Cria componentes de sincronização do Laravel SyncHub';

    private array $types = [
        'context' => [
            'templates' => [
                'SyncContext.php',
                'SourceGateway.php',
                'TargetGateway.php',
            ],
        ],

        'identity-resolver' => [
            'templates' => [
                'SourceIdentityResolver.php',
            ],
        ],

        'mapper' => [
            'templates' => [
                'SyncMapper.php',
            ],
        ],

        'validator' => [
            'templates' => [
                'SyncValidator.php',
            ],
        ],

        'dependency' => [
            'templates' => [
                'SyncDependencyChecker.php',
            ],
        ],

        'after-sync' => [
            'templates' => [
                'AfterSyncHandler.php',
            ],
        ],
    ];

    public function handle(Filesystem $files): int
    {
        $type = $this->argument('type');
        $input = $this->argument('name');

        if (!isset($this->types[$type])) {
            $this->error(
                "Tipo '{$type}' inválido."
            );

            $this->line('');
            $this->line(
                'Tipos disponíveis:'
            );

            foreach (array_keys($this->types) as $availableType) {
                $this->line("  - {$availableType}");
            }

            return self::FAILURE;
        }

        $segments = collect(explode('/', $input))
            ->map(
                fn($segment) => Str::studly($segment)
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

        foreach ($this->types[$type]['templates'] as $template) {
            $this->createFromStub(
                files: $files,
                path: $path,
                template: $template,
                className: $className,
                namespace: $segments->implode('\\\\'),
            );
        }

        return self::SUCCESS;
    }

    protected function createFromStub(
        Filesystem $files,
        string $path,
        string $template,
        string $className,
        string $namespace,
    ): void {
        $class = Str::replace(
            '.php',
            '',
            $template
        );

        $class = $className . $class;

        $file = "{$path}/{$class}.php";

        if (
            $files->exists($file)
            && !$this->option('force')
        ) {
            $this->warn(
                "{$class}.php já existe."
            );

            return;
        }

        $content = $this->stub(
            $template,
            $className,
            $namespace
        );

        $files->put(
            $file,
            $content
        );

        $this->info(
            "{$class}.php criado"
        );
    }

    protected function stub(
        string $file,
        string $name,
        string $namespace
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
            ],
            [
                "App\\Contexts\\{$namespace}",
                $name,
            ],
            file_get_contents($stub)
        );
    }
}
