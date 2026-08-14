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
    <!-- Chamada do Vite padrão do Laravel 12 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
    $statusValue = $process->status?->value;

    $statusClass = match ($process->status) {
    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::SUCCESS =>
    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60',

    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::ERROR,
    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::FAILED =>
    'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/60',

    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PROCESSING =>
    'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60',

    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PENDING =>
    'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800/60',

    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::WAITING_DEPENDENCY =>
    'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/60',

    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::OBSOLETE =>
    'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',

    default =>
    'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
    };
    @endphp

    {{-- Indicador de atualização em tempo real --}}
    <div
        id="live-indicator"
        class="fixed bottom-4 right-4 z-50 hidden items-center gap-2
               rounded-full border border-slate-200 dark:border-slate-700
               bg-white dark:bg-slate-800
               px-3 py-2 shadow-lg text-xs font-semibold
               text-slate-500 dark:text-slate-400">

        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>

        Atualizando...
    </div>

    <!-- Cabeçalho de Navegação -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-slate-200 dark:border-slate-800 pb-5">

        <div>
            <a
                href="{{ route('synchub.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors">
                &larr; Voltar para a lista
            </a>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-2">
                Processo #{{ $process->id }}
            </h1>

            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Tipo do processo:
                <span class="font-mono bg-slate-200/60 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs font-semibold">
                    {{ $process->context }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-4 sm:text-right">

            <form
                id="rerun-form"
                method="POST"
                action="{{ route('synchub.rerun', $process->id) }}"
                class="{{ in_array($statusValue, ['success', 'failed', 'error', 'obsolete'], true) ? '' : 'hidden' }}">

                @csrf

                <button
                    id="rerun-button"
                    type="submit"
                    class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">

                    <span id="rerun-button-label">
                        @if($process->status === \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::SUCCESS)
                        Forçar sincronização
                        @else
                        Tentar novamente
                        @endif
                    </span>

                </button>

            </form>

            <div>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                    Status Atual
                </span>

                <span
                    id="process-status"
                    data-status="{{ $process->status?->value }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-sm font-bold rounded-full border {{ $statusClass }}">

                    @if($process->status === \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PROCESSING)
                    <span
                        id="process-status-indicator"
                        class="w-2 h-2 rounded-full bg-amber-500 animate-pulse">
                    </span>
                    @else
                    <span
                        id="process-status-indicator"
                        class="hidden w-2 h-2 rounded-full bg-amber-500 animate-pulse">
                    </span>
                    @endif

                    <span id="process-status-label">
                        {{ $process->status->label() }}
                    </span>

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
                    <span class="mx-1">|</span>
                    {{ $relation->type->label() }}
                </div>
            </div>

            <a
                href="{{ route('synchub.show', $relation->parentProcess->id) }}"
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
                Ref ID: #{{ $process->entity_id }}
            </span>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                Etapa Atual
            </span>

            <span
                id="process-step"
                class="text-slate-800 dark:text-slate-200 font-mono text-sm font-bold block mt-2">
                {{ $process->current_step?->label() ?? $process->current_step?->value ?? '-' }}
            </span>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 block uppercase tracking-wider">
                Cache Interno
            </span>

            @if($process->source_payload)

            <span class="inline-flex mt-2 items-center px-2 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
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

            <span
                id="process-duration"
                class="text-slate-800 dark:text-slate-200 text-sm font-semibold block mt-2">

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
            {{ $process->error['message'] ?? 'Erro durante a sincronização.' }}
        </p>

        @if(!empty($process->error['errors']))

        <ul class="space-y-2 text-sm">

            @foreach($process->error['errors'] as $field => $messages)

            @foreach($messages as $message)

            <li class="flex gap-2">
                <span class="font-mono font-semibold text-rose-700 dark:text-rose-300">
                    {{ $field }}
                </span>

                <span>
                    {{ $message }}
                </span>
            </li>

            @endforeach

            @endforeach

        </ul>

        @endif

    </div>

    @endif

    <!-- Dependências -->
    @if($process->dependencies->isNotEmpty())

    <div class="mt-8 bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">

        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 block">
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
                        ID: #{{ $dependency->entity_id }}
                    </div>

                    @if($dependency->resolved)

                    <span class="text-xs text-emerald-600">
                        Resolvida em
                        {{ $dependency->resolved_at?->format('d/m/Y H:i:s') }}
                    </span>

                    @else

                    <span class="text-xs text-amber-600">
                        Pendente
                    </span>

                    @endif

                </div>

                @if($dependency->dependencyProcess)

                <a
                    href="{{ route('synchub.show', $dependency->dependencyProcess->id) }}"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Ver processo #{{ $dependency->dependencyProcess->id }}
                </a>

                @endif

            </div>

            @endforeach

        </div>

    </div>

    @endif

    <!-- Processos Disparados -->
    @if($process->triggeredRelations->isNotEmpty())

    {{-- Processos Disparados --}}
    <div class="mt-8 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">

        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">

            <div class="flex items-center justify-between">

                <div>
                    <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Processos Disparados
                    </span>

                    <p class="text-xs text-slate-400 mt-1">
                        Processos originados a partir deste processo.
                    </p>
                </div>

                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    {{ $triggeredProcesses->total() }}
                    {{ $triggeredProcesses->total() === 1 ? 'processo' : 'processos' }}
                </span>

            </div>

        </div>

        @if($triggeredProcesses->isNotEmpty())

        @php
        $groupedTriggeredProcesses = $triggeredProcesses
        ->groupBy(fn ($relation) =>
        $relation->childProcess?->context ?? 'Sem contexto'
        );
        @endphp

        <div class="divide-y divide-slate-200 dark:divide-slate-700">

            @foreach($groupedTriggeredProcesses as $context => $relations)

            {{-- Grupo --}}
            <div>

                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-700/30 flex items-center justify-between">

                    <div class="flex items-center gap-2">

                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>

                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                            {{ $context }}
                        </span>

                    </div>

                    <span class="text-xs text-slate-400">
                        {{ $relations->count() }}
                        {{ $relations->count() === 1 ? 'processo' : 'processos' }}
                    </span>

                </div>

                {{-- Processos do grupo --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">

                    @foreach($relations as $relation)

                    @php
                    $child = $relation->childProcess;
                    @endphp

                    @if($child)

                    <div class="px-5 py-4 flex items-center justify-between gap-4 hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-colors">

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span class="font-mono text-xs text-slate-400">
                                    #{{ $child->id }}
                                </span>

                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                    {{ $child->context }}
                                </span>

                            </div>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-400">

                                <span>
                                    Entidade #{{ $child->entity_id }}
                                </span>

                                <span class="text-slate-300 dark:text-slate-600">
                                    |
                                </span>

                                <span>
                                    {{ $relation->type->label() }}
                                </span>

                                @if($child->created_at)
                                <span class="text-slate-300 dark:text-slate-600">
                                    |
                                </span>

                                <span>
                                    {{ $child->created_at->format('d/m/Y H:i:s') }}
                                </span>
                                @endif

                            </div>

                        </div>

                        <a
                            href="{{ route('synchub.show', $child->id) }}"
                            class="shrink-0 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                            Ver processo
                        </a>

                    </div>

                    @endif

                    @endforeach

                </div>

            </div>

            @endforeach

        </div>

        {{-- Paginação --}}
        @if($triggeredProcesses->hasPages())

        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">

            {{ $triggeredProcesses->onEachSide(1)->links() }}

        </div>

        @endif

        @else

        <div class="px-5 py-8 text-center">

            <div class="text-sm text-slate-400 dark:text-slate-500">
                Nenhum processo foi disparado por este processo.
            </div>

        </div>

        @endif

    </div>

    @endif

    <!-- Dados Técnicos -->
    <div class="mt-10 space-y-8 mb-8">

        <div>
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                Payload Interno
            </h3>

            <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <pre class="bg-slate-900 text-emerald-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->source_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                Payload Mapeado
            </h3>

            <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <pre class="bg-slate-900 text-yellow-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->target_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2.5">
                Resposta Externa
            </h3>

            <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
                <pre class="bg-slate-900 text-sky-400 p-4 text-xs overflow-x-auto font-mono max-h-96"><code>{{ json_encode($process->target_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>

    </div>

    <!-- Histórico de Execução -->
    <div class="mt-10">

        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">
            Histórico de Execução (Logs)
        </h3>

        <div id="process-logs" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm divide-y divide-slate-200 dark:divide-slate-700 overflow-hidden">

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

    <script>
        const statusUrl = @json(route('synchub.status', $process->id));

        const initialStatus = @json($process->status?->value);

        const finishedStatuses = [
            'success',
            'failed',
            'error',
            'obsolete'
        ];

        const processAlreadyFinished = finishedStatuses.includes(initialStatus);

        let processInterval = null;
        let refreshing = false;
        let stopped = processAlreadyFinished;
        let lastLogId = null;

        const statusElement = document.getElementById('process-status');
        const statusLabel = document.getElementById('process-status-label');
        const statusIndicator = document.getElementById('process-status-indicator');
        const stepElement = document.getElementById('process-step');
        const durationElement = document.getElementById('process-duration');
        const liveIndicator = document.getElementById('live-indicator');

        const rerunForm = document.getElementById('rerun-form');
        const rerunButtonLabel = document.getElementById('rerun-button-label');

        const statusClasses = {
            success: [
                'bg-emerald-50',
                'text-emerald-700',
                'border-emerald-200',
                'dark:bg-emerald-950/40',
                'dark:text-emerald-400',
                'dark:border-emerald-800/60'
            ],

            error: [
                'bg-rose-50',
                'text-rose-700',
                'border-rose-200',
                'dark:bg-rose-950/40',
                'dark:text-rose-400',
                'dark:border-rose-800/60'
            ],

            failed: [
                'bg-rose-50',
                'text-rose-700',
                'border-rose-200',
                'dark:bg-rose-950/40',
                'dark:text-rose-400',
                'dark:border-rose-800/60'
            ],

            processing: [
                'bg-amber-50',
                'text-amber-700',
                'border-amber-200',
                'dark:bg-amber-950/40',
                'dark:text-amber-400',
                'dark:border-amber-800/60'
            ],

            pending: [
                'bg-blue-50',
                'text-blue-700',
                'border-blue-200',
                'dark:bg-blue-950/40',
                'dark:text-blue-400',
                'dark:border-blue-800/60'
            ],

            waiting_dependency: [
                'bg-purple-50',
                'text-purple-700',
                'border-purple-200',
                'dark:bg-purple-950/40',
                'dark:text-purple-400',
                'dark:border-purple-800/60'
            ],

            obsolete: [
                'bg-slate-100',
                'text-slate-600',
                'border-slate-200',
                'dark:bg-slate-800',
                'dark:text-slate-400',
                'dark:border-slate-700'
            ]
        };

        const allStatusClasses = Object.values(statusClasses).flat();

        function updateStatus(status, label) {

            if (!statusElement) {
                return;
            }

            statusElement.classList.remove(...allStatusClasses);

            const classes = statusClasses[status] ?? [
                'bg-slate-100',
                'text-slate-700',
                'border-slate-200',
                'dark:bg-slate-800',
                'dark:text-slate-400',
                'dark:border-slate-700'
            ];

            statusElement.classList.add(...classes);

            if (statusLabel) {
                statusLabel.textContent = label ?? status;
            }

            if (statusIndicator) {
                if (status === 'processing') {
                    statusIndicator.classList.remove('hidden');
                } else {
                    statusIndicator.classList.add('hidden');
                }
            }

            statusElement.dataset.status = status;


            /*
             * Botão de tentar novamente
             */
            const finishedStatuses = [
                'success',
                'failed',
                'error',
                'obsolete'
            ];

            if (rerunForm) {

                if (finishedStatuses.includes(status)) {
                    rerunForm.classList.remove('hidden');
                } else {
                    rerunForm.classList.add('hidden');
                }
            }

            if (rerunButtonLabel) {

                if (status === 'success') {
                    rerunButtonLabel.textContent = 'Forçar sincronização';
                } else if (
                    status === 'failed' ||
                    status === 'error' ||
                    status === 'obsolete'
                ) {
                    rerunButtonLabel.textContent = 'Tentar novamente';
                }
            }
        }

        function updateStep(step) {

            if (!stepElement) {
                return;
            }

            stepElement.textContent = step?.label ?? step?.value ?? step ?? '-';
        }

        function updateDuration(data) {

            if (!durationElement) {
                return;
            }

            if (data.duration_seconds !== null && data.duration_seconds !== undefined) {

                durationElement.innerHTML = `
                <span class="font-mono text-lg font-bold">
                    ${data.duration_seconds}
                </span>

                <span class="text-xs text-slate-400">
                    segundos
                </span>
            `;

                return;
            }

            durationElement.innerHTML = `
            <span class="text-slate-400 text-xs">
                Em andamento
            </span>
        `;
        }

        function showLiveIndicator() {

            if (!liveIndicator) {
                return;
            }

            liveIndicator.classList.remove('hidden');
            liveIndicator.classList.add('flex');
        }

        function hideLiveIndicator() {

            if (!liveIndicator) {
                return;
            }

            liveIndicator.classList.add('hidden');
            liveIndicator.classList.remove('flex');
        }

        function updateLogs(logs) {

            const logsElement = document.getElementById('process-logs');

            if (!logsElement || !logs) {
                return;
            }

            if (logs.length === 0) {
                if (lastLogId !== 0) {
                    logsElement.innerHTML = `
                <div class="p-6 text-center text-sm text-slate-400 dark:text-slate-500">
                    Nenhum log registrado para este processo.
                </div>
            `;

                    lastLogId = 0;
                }

                return;
            }

            const newestLogId = logs[logs.length - 1]?.id ?? null;

            // Nada mudou
            if (lastLogId === newestLogId) {
                return;
            }

            lastLogId = newestLogId;

            logsElement.innerHTML = logs.map(log => {

                let payloadHtml = '';

                if (log.payload) {

                    let payload;

                    try {
                        payload = JSON.stringify(log.payload, null, 2);
                    } catch {
                        payload = String(log.payload);
                    }

                    payloadHtml = `
                <div class="mt-3 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">

                    <pre class="bg-slate-900 text-emerald-400 p-3.5 text-xs overflow-x-auto font-mono leading-relaxed"><code>${escapeHtml(payload)}</code></pre>

                </div>
            `;
                }

                return `
            <div class="p-5 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <span class="font-semibold text-sm text-slate-700 dark:text-slate-200">
                        ${escapeHtml(log.message ?? '')}
                    </span>

                    <span class="text-xs font-mono text-slate-400 dark:text-slate-500">
                        ${escapeHtml(log.created_at ?? '')}
                    </span>

                </div>

                ${payloadHtml}

            </div>
        `;

            }).join('');
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function refreshProcess() {

            if (stopped) {
                return false;
            }

            if (refreshing) {
                return true;
            }

            refreshing = true;

            showLiveIndicator();

            try {

                const response = await fetch(statusUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    console.error(
                        'Erro ao consultar status:',
                        response.status
                    );

                    return true;
                }

                const data = await response.json();

                console.log('Status recebido:', data);

                updateStatus(
                    data.status,
                    data.status_label
                );

                updateStep(data.current_step);

                updateDuration(data);

                updateLogs(data.logs);

                /*
                 * PROCESSO FINALIZADO
                 */
                if (data.finished === true) {

                    console.log('Processo finalizado. Recarregando página...');

                    stopped = true;

                    if (processInterval !== null) {
                        clearTimeout(processInterval);
                        processInterval = null;
                    }

                    hideLiveIndicator();

                    setTimeout(() => {
                        window.location.reload();
                    }, 300);

                    return false;
                }

                return true;

            } catch (error) {

                console.error(
                    'Erro ao atualizar processo:',
                    error
                );

                return true;

            } finally {

                refreshing = false;

                setTimeout(() => {

                    if (!stopped) {
                        hideLiveIndicator();
                    }

                }, 300);
            }
        }

        /*
         * Polling
         */
        async function poll() {

            if (stopped) {
                console.log('Polling já está parado.');
                return;
            }

            const shouldContinue = await refreshProcess();

            /*
             * Se refreshProcess() informou que terminou,
             * NÃO agenda outra requisição.
             */
            if (!shouldContinue || stopped) {
                console.log('Polling encerrado.');
                return;
            }

            processInterval = setTimeout(
                poll,
                2000
            );
        }

        /*
        * Primeira execução imediata
        *
        * Se a página já foi carregada com um processo finalizado,
        * não inicia o polling e não recarrega a página.
        */
        if (!processAlreadyFinished) {
            poll();
        }
    </script>
</body>

</html>