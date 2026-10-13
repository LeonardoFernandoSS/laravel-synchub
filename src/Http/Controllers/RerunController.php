<?php

namespace Synchub\LaravelSynchub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Synchub\LaravelSynchub\Application\Sync\Handlers\RerunSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcess;

class RerunController extends Controller
{
    public function __construct(
        private RerunSyncHandler $rerunSyncHandler,
    ) {}

    public function rerun(int $id)
    {
        $process = SyncProcess::findOrFail($id);

        abort_unless(
            $this->canRerun($process),
            422,
            'Processo não pode ser executado novamente.'
        );

        $rerun = $this->rerunSyncHandler->handle($process->id);

        return redirect()->route(
            'synchub.show',
            $rerun->id,
        );
    }

    public function rerunBatch(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:sync_processes,id',
            ],
        ], [
            'ids.required' => 'Selecione ao menos um processo.',
            'ids.min' => 'Selecione ao menos um processo.',
            'ids.*.integer' => 'Um dos processos selecionados é inválido.',
            'ids.*.distinct' => 'Existem processos duplicados na seleção.',
            'ids.*.exists' => 'Um dos processos selecionados não existe.',
        ]);

        $filters = $request->only([
            'context',
            'step',
            'source_key',
        ]);

        $processes = SyncProcess::query()
            ->whereIn('id', $validated['ids'])
            ->get();

        $rerunIds = [];
        $skipped = 0;

        foreach ($processes as $process) {
            if (! $this->canRerun($process)) {
                $skipped++;
                
                continue;
            }

            $rerun = $this->rerunSyncHandler->handle($process->id);

            $rerunIds[] = $rerun->id;
        }

        if (count($rerunIds) === 0) {
            return redirect()
                ->route('synchub.index', $filters)
                ->with(
                    'error',
                    'Nenhum dos processos selecionados pode ser executado novamente.'
                );
        }

        $message = count($rerunIds)
            . ' processo(s) enviado(s) para reprocessamento.';

        if ($skipped > 0) {
            $message .= " {$skipped} processo(s) ignorado(s) por não permitirem reprocessamento.";
        }

        return redirect()
            ->route('synchub.index', $filters)
            ->with('success', $message);
    }

    private function canRerun(SyncProcess $process): bool
    {
        return in_array(
            $process->status?->value,
            [
                'success',
                'failed',
                'error',
                'obsolete',
            ],
            true,
        );
    }
}
