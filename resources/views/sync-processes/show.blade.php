<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Processo #{{ $process->id }}</title>

    <!-- Chamada do Vite padrão do Laravel 12 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 antialiased min-h-screen p-6 sm:p-10 dark:bg-slate-900 dark:text-slate-100">
    <div class="max-w-6xl mx-auto">

        @php
        $statusValue = $process->status instanceof \BackedEnum ? $process->status->value : $process->status;

        $statusClass = match($statusValue) {
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60',
        'error', 'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/60',
        'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60',
        'pending' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800/60',
        'waiting_dependency' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400',
        'obsolete' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400',
        default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
        };

        $statusLabel = match($statusValue) {
        'success' => 'Sucesso',
        'error' => 'Erro',
        'failed' => 'Falhou',
        'processing' => 'Processando',
        'pending' => 'Pendente',
        'waiting_dependency' => 'Aguardando Dependência',
        'obsolete' => 'Obsoleto',
        default => ucfirst($statusValue ?? ''),
        };
        @endphp

        <!-- Cabeçalho de Navegação -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-slate-200 dark:border-slate-800 pb-5">

            <div>
                <a href="{{ route('sync-processes.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">
                    &larr; Voltar para a lista
                </a>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-2">
                    Processo #{{ $process->id }}
                </h1>

                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    Tipo do processo:
                    <span class="font-mono bg-slate-200/60 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs font-semibold">
                        {{ $process->type }}
                    </span>
                </p>
            </div>


            <div class="flex items-center gap-4 sm:text-right">

                @if(in_array($statusValue, [
                'success',
                'failed',
                'error',
                'obsolete'
                ]))

                <form
                    method="POST"
                    action="{{ route('sync-processes.rerun', $process->id) }}">

                    @csrf

                    <button
                        type="submit"
                        class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">

                        @if($statusValue === 'success')
                        Forçar sincronização
                        @else
                        Reexecutar
                        @endif

                    </button>

                </form>

                @endif


                @php
                $statusValue = $process->status instanceof \BackedEnum
                ? $process->status->value
                : $process->status;
                @endphp

                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                        Status Atual
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-sm font-bold rounded-full border {{ $statusClass }}">
                        @if($statusValue === 'processing')
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        @endif

                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        @if($process->parentRelations->isNotEmpty())

        <div class="mt-6 bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">

            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                Originado por
            </span>

            @foreach($process->parentRelations as $relation)

            <div class="mt-3 flex items-center justify-between">

                <div>

                    <div class="font-semibold">
                        {{ $relation->parentProcess->context }}
                    </div>

                    <div class="text-xs text-slate-400">
                        Processo #{{ $relation->parentProcess->id }}
                    </div>

                </div>

                <a
                    href="{{ route('sync-processes.show', $relation->parentProcess->id) }}"
                    class="text-xs font-semibold text-indigo-600">

                    Ver processo

                </a>

            </div>

            @endforeach

        </div>

        @endif

        <!-- Painel de Métricas Rápidas -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                    Contexto
                </span>

                <span class="text-slate-800 dark:text-slate-200 font-semibold block mt-1">
                    {{ $process->context }}
                </span>

                <span class="text-xs font-mono text-slate-400 mt-0.5 block">
                    Ref ID: #{{ $process->context_id }}
                </span>
            </div>


            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                    Etapa Atual
                </span>

                <span class="text-slate-800 dark:text-slate-200 font-mono text-sm font-bold block mt-2">
                    {{ $process->current_step?->value ?? $process->current_step ?? '-' }}
                </span>
            </div>


            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                    Cache Interno
                </span>

                @if($process->internal_payload)

                <span class="inline-flex mt-2 items-center px-2 py-1 rounded-full text-xs font-semibold
                bg-emerald-50 text-emerald-700
                dark:bg-emerald-950/40 dark:text-emerald-400">

                    Disponível
                </span>

                @if($process->payload_cached_at)
                <div class="text-xs text-slate-400 mt-2">
                    {{ $process->payload_cached_at->format('d/m/Y H:i:s') }}
                </div>
                @endif

                @else

                <span class="text-xs text-slate-400 mt-2 block">
                    Não armazenado
                </span>

                @endif
            </div>


            <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                    Duração
                </span>

                <span class="text-slate-800 dark:text-slate-200 text-sm font-semibold block mt-2">

                    @if($process->started_at && $process->finished_at)

                    <span class="font-mono text-lg font-bold">
                        {{ $process->started_at->diffInSeconds($process->finished_at) }}
                    </span>

                    <span class="text-xs text-slate-400">
                        segundos
                    </span>

                    @else

                    <span class="text-slate-400 text-xs">
                        Em andamento
                    </span>

                    @endif

                </span>
            </div>

        </div>

        <!-- Seção de Alerta de Erro -->
        @if($process->error)
        <div class="bg-rose-100/50 dark:bg-rose-950/40 p-4 rounded-lg border border-rose-200/60 dark:border-rose-900/40 mt-1">

            <p class="font-semibold text-sm mb-3">
                {{ $process->error['message'] }}
            </p>

            @if(!empty($process->error['errors']))
            <ul class="space-y-2 text-sm">
                @foreach($process->error['errors'] as $field => $messages)
                @foreach($messages as $message)
                <li class="flex gap-2">
                    <span class="font-mono font-semibold text-rose-700 dark:text-rose-300">
                        {{ $field }}
                    </span>
                    <span>{{ $message }}</span>
                </li>
                @endforeach
                @endforeach
            </ul>
            @endif

        </div>
        @endif

        <!-- Seção de Alerta de Dependências -->
        @if($process->dependencies->isNotEmpty())
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">

            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider mb-3">
                Dependências
            </span>

            <div class="space-y-3">

                @foreach($process->dependencies as $dependency)

                <div class="flex items-center justify-between">

                    <div>
                        <div class="font-semibold text-sm">
                            {{ $dependency->context }}
                        </div>

                        <div class="text-xs text-slate-400 font-mono">
                            ID: #{{ $dependency->context_id }}
                        </div>

                        @if($dependency->resolved)
                        <span class="text-xs text-emerald-600">
                            Resolvida em
                            {{ $dependency->resolved_at->format('d/m/Y H:i:s') }}
                        </span>
                        @else
                        <span class="text-xs text-amber-600">
                            Pendente
                        </span>
                        @endif
                    </div>


                    @if($dependency->dependencyProcess)

                    <a
                        href="{{ route('sync-processes.show', $dependency->dependencyProcess->id) }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Ver processo #{{ $dependency->dependencyProcess->id }}
                    </a>

                    @endif

                </div>

                @endforeach

            </div>

        </div>
        @endif

        @if($process->triggeredRelations->isNotEmpty())
        <div class="mt-8 bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">

            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider mb-3">
                Processos Disparados
            </span>

            <div class="space-y-3">

                @foreach($process->triggeredRelations as $relation)

                @php
                $child = $relation->childProcess;
                @endphp

                <div class="flex items-center justify-between">

                    <div>

                        <div class="font-semibold text-sm">
                            {{ $child->context }}
                        </div>

                        <div class="text-xs text-slate-400 font-mono">
                            Processo #{{ $child->id }}
                        </div>

                        <div class="text-xs text-slate-400">
                            Contexto #{{ $child->context_id }}
                        </div>

                    </div>

                    <a
                        href="{{ route('sync-processes.show', $child->id) }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">

                        Ver processo

                    </a>

                </div>

                @endforeach

            </div>

        </div>
        @endif

        <!-- Dados Técnicos -->
        <div class="mt-10 space-y-8 mb-8">
            <div>
                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                    Payload Interno
                </h3>

                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <pre class="bg-slate-900 text-emerald-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->internal_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                    Payload Mapeado
                </h3>

                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <pre class="bg-slate-900 text-yellow-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->mapped_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                    Resposta Externa
                </h3>

                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                    <pre class="bg-slate-900 text-sky-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->external_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        </div>

        <!-- Histórico de Execução (Logs) -->
        <div class="mt-10">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">
                Histórico de Execução (Logs)
            </h3>

            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm divide-y divide-slate-200 dark:divide-slate-700 overflow-hidden">
                @forelse($process->logs as $log)
                <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">
                            {{ $log->message }}
                        </span>
                        <span class="text-xs font-mono text-slate-400 dark:text-slate-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </span>
                    </div>

                    @if($log->payload)
                    <div class="mt-3 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <pre class="bg-slate-900 text-emerald-400 p-3.5 text-xs overflow-x-auto font-mono leading-relaxed"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                    @endif
                </div>
                @empty
                <div class="p-6 text-center text-sm text-slate-400 dark:text-slate-500">
                    Nenhum log registrado para este processo.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</body>

</html>