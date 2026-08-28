<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Processos de Sincronização</title>

    <!-- Chamada correta do Vite para carregar seu Tailwind v4 local -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .process-checkbox,
        #select-all {
            position: relative;
        }

        .process-checkbox:checked,
        #select-all:checked {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M3.5 8.5L6.5 11.5L12.5 4.5' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-size: 14px 14px;
            background-position: center;
            background-repeat: no-repeat;
        }

        #select-all:indeterminate {
            background-color: rgb(79 70 229);
            border-color: rgb(79 70 229);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 8H12' stroke='white' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-size: 12px 12px;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>

</head>

<body class="bg-slate-50 text-slate-900 antialiased min-h-screen p-6 sm:p-10 dark:bg-slate-900 dark:text-slate-100">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <div class="max-w-7xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Processos de Sincronização
            </h1>

            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Monitore o histórico e o status de integração dos dados.
            </p>
        </div>

        {{-- Abas de Status --}}
        <div class="mb-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="overflow-x-auto">
                <nav class="flex min-w-max" aria-label="Status dos processos">

                    {{-- Todos --}}
                    <a
                        href="{{ route('synchub.index', request()->except('status', 'page')) }}"
                        class="
                    inline-flex items-center gap-2 px-5 py-3
                    text-sm font-semibold border-b-2 transition-colors
                    {{ !$status
                        ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400'
                        : 'text-slate-500 border-transparent hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-200'
                    }}
                ">
                        Todos

                        <span class="
                    inline-flex items-center justify-center
                    min-w-5 h-5 px-1.5 rounded-full text-[11px] font-bold
                    {{ !$status
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400'
                        : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'
                    }}
                ">
                            {{ $statusCounts['all'] ?? 0 }}
                        </span>
                    </a>

                    @foreach($statuses as $item)

                    @php
                    $isActive = $status === $item->value;

                    $tabClass = match ($item) {
                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::SUCCESS =>
                    $isActive
                    ? 'text-emerald-600 border-emerald-500 dark:text-emerald-400 dark:border-emerald-400'
                    : 'text-slate-500 border-transparent hover:text-emerald-600 hover:border-emerald-300 dark:text-slate-400 dark:hover:text-emerald-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::ERROR,
                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::FAILED =>
                    $isActive
                    ? 'text-rose-600 border-rose-500 dark:text-rose-400 dark:border-rose-400'
                    : 'text-slate-500 border-transparent hover:text-rose-600 hover:border-rose-300 dark:text-slate-400 dark:hover:text-rose-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PROCESSING =>
                    $isActive
                    ? 'text-amber-600 border-amber-500 dark:text-amber-400 dark:border-amber-400'
                    : 'text-slate-500 border-transparent hover:text-amber-600 hover:border-amber-300 dark:text-slate-400 dark:hover:text-amber-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PENDING =>
                    $isActive
                    ? 'text-blue-600 border-blue-500 dark:text-blue-400 dark:border-blue-400'
                    : 'text-slate-500 border-transparent hover:text-blue-600 hover:border-blue-300 dark:text-slate-400 dark:hover:text-blue-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::WAITING_DEPENDENCY =>
                    $isActive
                    ? 'text-purple-600 border-purple-500 dark:text-purple-400 dark:border-purple-400'
                    : 'text-slate-500 border-transparent hover:text-purple-600 hover:border-purple-300 dark:text-slate-400 dark:hover:text-purple-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::OBSOLETE =>
                    $isActive
                    ? 'text-slate-700 border-slate-500 dark:text-slate-200 dark:border-slate-400'
                    : 'text-slate-500 border-transparent hover:text-slate-700 hover:border-slate-300 dark:text-slate-400 dark:hover:text-slate-200',
                    };

                    $countClass = match ($item) {
                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::SUCCESS =>
                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::ERROR,
                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::FAILED =>
                    'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PROCESSING =>
                    'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PENDING =>
                    'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::WAITING_DEPENDENCY =>
                    'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400',

                    \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::OBSOLETE =>
                    'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
                    };
                    @endphp

                    <a
                        href="{{ route('synchub.index', array_merge(
                        request()->except('page'),
                        ['status' => $item->value]
                    )) }}"
                        class="
                        inline-flex items-center gap-2 px-5 py-3
                        text-sm font-semibold border-b-2 transition-colors
                        {{ $tabClass }}
                    ">
                        {{ $item->label() }}

                        <span class="
                        inline-flex items-center justify-center
                        min-w-5 h-5 px-1.5 rounded-full text-[11px] font-bold
                        {{ $countClass }}
                    ">
                            {{ $statusCounts[$item->value] ?? 0 }}
                        </span>
                    </a>

                    @endforeach

                </nav>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="mb-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">

            <form
                method="GET"
                action="{{ route('synchub.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4">

                {{-- Mantém o status selecionado --}}
                @if($status)
                <input
                    type="hidden"
                    name="status"
                    value="{{ $status }}">
                @endif

                {{-- Contexto --}}
                <div class="sm:col-span-2 lg:col-span-4">
                    <label
                        for="context"
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Contexto
                    </label>

                    <select
                        id="context"
                        name="context"
                        class="w-full rounded-lg border border-slate-200 dark:border-slate-700
                            bg-white dark:bg-slate-900
                            text-sm text-slate-700 dark:text-slate-200
                            focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Todos os contextos
                        </option>

                        @foreach($contexts as $item)
                        <option
                            value="{{ $item }}"
                            @selected($context===$item)>
                            {{ $item }}
                        </option>
                        @endforeach

                    </select>
                </div>

                {{-- Etapa --}}
                <div class="lg:col-span-4">
                    <label
                        for="step"
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Etapa
                    </label>

                    <select
                        id="step"
                        name="step"
                        class="w-full rounded-lg border border-slate-200 dark:border-slate-700
                            bg-white dark:bg-slate-900
                            text-sm text-slate-700 dark:text-slate-200
                            focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="">
                            Todas as etapas
                        </option>

                        @foreach($steps as $item)
                        <option
                            value="{{ $item->value }}"
                            @selected($step===$item->value)>
                            {{ $item->label() }}
                        </option>
                        @endforeach

                    </select>
                </div>

                {{-- ID da entidade --}}
                <div class="lg:col-span-2">
                    <label
                        for="source_id"
                        class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        ID da entidade
                    </label>

                    <input
                        type="number"
                        id="source_id"
                        name="source_id"
                        value="{{ $sourceId }}"
                        min="1"
                        placeholder="Ex.: 123"
                        class="w-full rounded-lg border border-slate-200 dark:border-slate-700
                            bg-white dark:bg-slate-900
                            text-sm text-slate-700 dark:text-slate-200
                            focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- Botões --}}
                <div class="lg:col-span-2 flex items-end gap-2">

                    <button
                        type="submit"
                        class="flex-1 inline-flex items-center justify-center
                            px-4 py-2 rounded-lg
                            bg-indigo-600 text-white
                            text-sm font-semibold
                            hover:bg-indigo-700
                            focus:outline-none focus:ring-2 focus:ring-indigo-500
                            whitespace-nowrap">
                        Filtrar
                    </button>

                    @if($status || $context || $step || $sourceId)

                    <a
                        href="{{ route('synchub.index') }}"
                        class="flex-1 inline-flex items-center justify-center
                                px-4 py-2 rounded-lg
                                border border-slate-200
                                dark:border-slate-700
                                text-sm font-semibold
                                text-slate-600 dark:text-slate-300
                                hover:bg-slate-50 dark:hover:bg-slate-700
                                whitespace-nowrap">
                        Limpar
                    </a>

                    @endif

                </div>

            </form>
        </div>

        <div
            id="bulk-actions"
            class="hidden mb-4 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-900/50 rounded-xl px-4 py-3">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div class="text-sm text-indigo-700 dark:text-indigo-300">
                    <span
                        id="selected-count"
                        class="font-bold">
                        0
                    </span>

                    processos selecionados
                </div>

                <button
                    type="button"
                    id="bulk-rerun-button"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg
                        bg-indigo-600 text-white text-sm font-semibold
                        hover:bg-indigo-700
                        disabled:opacity-50 disabled:cursor-not-allowed">

                    Tentar novamente selecionados
                </button>
            </div>
        </div>

        @php
            $rerunnableStatuses = [
                'success',
                'failed',
                'error',
                'obsolete',
            ];

            $canRerun = in_array(
                request()->query('status'),
                $rerunnableStatuses,
                true
            );
        @endphp

        {{-- Tabela --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">

                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            @if($canRerun)
                                <th scope="col" class="px-4 py-4 w-12 text-center">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        class="
                                            peer
                                            relative
                                            appearance-none
                                            w-5 h-5
                                            rounded-md
                                            border-2 border-slate-300
                                            dark:border-slate-600
                                            bg-white dark:bg-slate-800
                                            cursor-pointer
                                            transition-all duration-150
                                            hover:border-indigo-400
                                            hover:bg-indigo-50
                                            dark:hover:border-indigo-500
                                            dark:hover:bg-indigo-950/30
                                            checked:bg-indigo-600
                                            checked:border-indigo-600
                                            focus:outline-none
                                            focus:ring-2
                                            focus:ring-indigo-500/30
                                            focus:ring-offset-1
                                            dark:focus:ring-offset-slate-800
                                        "
                                    >
                                </th>
                            @endif
                            <th scope="col" class="px-6 py-4 w-16">
                                ID
                            </th>

                            <th scope="col" class="px-6 py-4">
                                Tipo / Contexto
                            </th>

                            <th scope="col" class="px-6 py-4 w-32">
                                Status
                            </th>

                            <th scope="col" class="px-6 py-4 w-40">
                                Etapa
                            </th>

                            <th scope="col" class="px-6 py-4 w-28 text-center">
                                Cache
                            </th>

                            <th scope="col" class="px-6 py-4 w-48">
                                Iniciado Em
                            </th>

                            <th scope="col" class="px-6 py-4 w-28 text-right">
                                Ações
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-medium">

                        @forelse($processes as $process)

                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">

                            @if($canRerun)
                                <td class="px-4 py-4 text-center">
                                    @if(in_array($process->status?->value, [
                                    'success',
                                    'failed',
                                    'error',
                                    'obsolete',
                                    ], true))

                                    <input
                                        type="checkbox"
                                        name="process_ids[]"
                                        value="{{ $process->id }}"
                                        class="
                                            process-checkbox
                                            appearance-none
                                            w-5 h-5
                                            rounded-md
                                            border-2 border-slate-300
                                            dark:border-slate-600
                                            bg-white dark:bg-slate-800
                                            cursor-pointer
                                            transition-all duration-150
                                            hover:border-indigo-400
                                            hover:bg-indigo-50
                                            dark:hover:border-indigo-500
                                            dark:hover:bg-indigo-950/30
                                            checked:bg-indigo-600
                                            checked:border-indigo-600
                                            focus:outline-none
                                            focus:ring-2
                                            focus:ring-indigo-500/30
                                            focus:ring-offset-1
                                            dark:focus:ring-offset-slate-800
                                        "
                                    >
                                    @endif
                                </td>
                            @endif

                            {{-- ID --}}
                            <td class="px-6 py-4 text-slate-400 dark:text-slate-500 font-mono">
                                #{{ $process->id }}
                            </td>

                            {{-- Tipo / Contexto --}}
                            <td class="px-6 py-4">
                                <div class="text-slate-900 dark:text-white font-semibold">
                                    {{ $process->type }}
                                </div>

                                <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $process->context }}
                                    <span class="text-slate-300 dark:text-slate-600">
                                        ·
                                    </span>
                                    ID: {{ $process->source_key }}
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @php
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

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">

                                    @if($process->status === \Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus::PROCESSING)
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @endif

                                    {{ $process->status->label() }}

                                </span>
                            </td>

                            {{-- Etapa --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-mono rounded-md bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                                    {{ $process->current_step?->label() ?? '-' }}
                                </span>
                            </td>

                            {{-- Cache --}}
                            <td class="px-6 py-4 text-center">

                                @if($process->source_payload)

                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                    Disponível
                                </span>

                                @else

                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    -
                                </span>

                                @endif

                            </td>

                            {{-- Data --}}
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">

                                <div>
                                    {{ $process->started_at?->format('d/m/Y H:i:s') ?? '-' }}
                                </div>

                                @if($process->finished_at && $process->started_at)

                                <div class="text-xs text-slate-400 mt-1">
                                    {{ $process->finished_at->diffForHumans($process->started_at, true) }}
                                </div>

                                @endif

                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">

                                <a
                                    href="{{ route('synchub.show', $process->id) }}"
                                    class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                    Ver Detalhes
                                </a>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td
                                colspan="7"
                                class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                Nenhum processo de sincronização encontrado.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($processes->hasPages())

            <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $processes->links() }}
            </div>

            @endif

        </div>
    </div>

    <script>
        const selectAll = document.getElementById('select-all');

        const processCheckboxes = Array.from(
            document.querySelectorAll('.process-checkbox')
        );

        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');
        const bulkRerunButton = document.getElementById('bulk-rerun-button');

        function getSelectedIds() {
            return processCheckboxes
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
        }

        function updateBulkActions() {

            const selectedIds = getSelectedIds();

            const count = selectedIds.length;

            if (selectedCount) {
                selectedCount.textContent = count;
            }

            if (bulkActions) {

                if (count > 0) {
                    bulkActions.classList.remove('hidden');
                } else {
                    bulkActions.classList.add('hidden');
                }
            }

            if (bulkRerunButton) {
                bulkRerunButton.disabled = count === 0;
            }

            if (selectAll) {

                const total = processCheckboxes.length;

                selectAll.checked =
                    total > 0 &&
                    count === total;

                selectAll.indeterminate =
                    count > 0 &&
                    count < total;
            }
        }

        /*
         * Selecionar todos
         */
        selectAll?.addEventListener('change', function() {

            processCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });

            updateBulkActions();
        });

        /*
         * Seleção individual
         */
        processCheckboxes.forEach(checkbox => {

            checkbox.addEventListener(
                'change',
                updateBulkActions
            );

        });

        /*
         * Rerun em lote
         */
        bulkRerunButton?.addEventListener('click', async function() {

            const ids = getSelectedIds();

            if (ids.length === 0) {
                return;
            }

            const confirmed = confirm(
                `Deseja tentar novamente ${ids.length} processo(s) selecionado(s)?`
            );

            if (!confirmed) {
                return;
            }

            bulkRerunButton.disabled = true;

            const originalText =
                bulkRerunButton.textContent;

            bulkRerunButton.textContent =
                'Iniciando...';

            try {

                const response = await fetch(
                    @json(route('synchub.rerun.batch')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',

                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content'),
                        },
                        body: JSON.stringify({
                            ids: ids,
                        }),
                    }
                );

                const data = await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ??
                        'Não foi possível executar o rerun.'
                    );
                }

                /*
                 * Limpa seleção
                 */
                processCheckboxes.forEach(
                    checkbox => checkbox.checked = false
                );

                updateBulkActions();

                /*
                 * Atualiza a lista
                 */
                window.location.reload();

            } catch (error) {

                console.error(
                    'Erro no rerun em lote:',
                    error
                );

                alert(
                    error.message ??
                    'Erro ao tentar novamente os processos.'
                );

                bulkRerunButton.disabled = false;

                bulkRerunButton.textContent =
                    originalText;
            }
        });

        updateBulkActions();
    </script>

</body>

</html>