<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processos de Sincronização</title>

    <!-- Chamada correta do Vite para carregar seu Tailwind v4 local -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-900 antialiased min-h-screen p-6 sm:p-10 dark:bg-slate-900 dark:text-slate-100">

    <div class="max-w-7xl mx-auto">
        <!-- Cabeçalho -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Processos de Sincronização</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Monitore o histórico e o status de integração dos dados.</p>
        </div>

        <!-- Tabela -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16">ID</th>
                            <th scope="col" class="px-6 py-4">Tipo / Contexto</th>
                            <th scope="col" class="px-6 py-4 w-32">Status</th>
                            <th scope="col" class="px-6 py-4 w-40">Etapa</th>
                            <th scope="col" class="px-6 py-4 w-28 text-center">Cache</th>
                            <th scope="col" class="px-6 py-4 w-48">Iniciado Em</th>
                            <th scope="col" class="px-6 py-4 w-28 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700 font-medium">
                        @forelse($processes as $process)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-slate-400 dark:text-slate-500 font-mono">
                                #{{ $process->id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 dark:text-white font-semibold">{{ $process->type }}</div>
                                <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $process->context }} (ID: {{ $process->context_id }})
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                // Obtendo o valor em string do Enum para o match
                                $statusValue = $process->status instanceof \BackedEnum ? $process->status->value : $process->status;

                                $statusClass = match($statusValue) {
                                'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60',
                                'error', 'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/60',
                                'processing' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60',
                                'pending' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800/60',
                                'waiting_dependency' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/60',
                                'obsolete' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
                                default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
                                };

                                $statusLabel = match($statusValue) {
                                'success' => 'Sucesso',
                                'error' => 'Erro',
                                'failed' => 'Falhou',
                                'processing' => 'Processando',
                                'pending' => 'Pendente',
                                'waiting_dependency' => 'Aguardando Dep.',
                                'obsolete' => 'Obsoleto',
                                default => ucfirst($statusValue ?? ''),
                                };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                    @if($statusValue === 'processing')
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @endif
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-slate-600 dark:text-slate-400">
                                <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                                    {{ $process->current_step?->value ?? $process->current_step ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">

                                @if($process->internal_payload)
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">
                                    Disponível
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                    -
                                </span>
                                @endif

                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-mono text-xs">
                                <div>
                                    {{ $process->started_at?->format('d/m/Y H:i:s') ?? '-' }}
                                </div>

                                @if($process->finished_at)

                                <div class="text-xs text-slate-400 mt-1">
                                    {{ $process->finished_at->diffForHumans($process->started_at, true) }}
                                </div>

                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('sync-processes.show', $process->id) }}" class="inline-flex items-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                Nenhum processo de sincronização encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Rodapé com Paginação Embutida -->
            @if($processes->hasPages())
            <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $processes->links() }}
            </div>
            @endif
        </div>
    </div>

</body>

</html>