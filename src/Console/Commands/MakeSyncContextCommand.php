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
        {--force : Sobrescreve arquivos existentes}
    ';


    protected $description = 'Cria um contexto de sincronização';


    public function handle(
        Filesystem $files
    ): int {

        $input = $this->argument('name');


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



        $files->makeDirectory(
            $path,
            0755,
            true
        );



        $templates = [

            'SyncContext.php',
            'Mapper.php',
            'Validator.php',
            'DependencyChecker.php',
            'AfterSyncHandler.php',

            // gateways
            'InternalGateway.php',
            'ExternalGateway.php',

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
                $segments
                    ->implode('\\')
            );



            $files->put(
                $file,
                $content
            );



            $this->info(
                "{$class}.php criado"
            );
        }



        return self::SUCCESS;
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
                '{{ name }}'
            ],
            [
                "App\\Contexts\\{$namespace}",
                $name
            ],
            file_get_contents($stub)
        );
    }
}