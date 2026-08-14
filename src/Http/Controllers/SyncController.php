<?php

namespace Synchub\LaravelSynchub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\RerunSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartBatchSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStatus;
use Synchub\LaravelSynchub\Domain\Sync\Enums\SyncProcessStep;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcess;

class SyncController extends Controller
{
    public function __construct(
        private StartSyncHandler $startSyncHandler,
        private StartBatchSyncHandler $startBatchSyncHandler,
        private RerunSyncHandler $rerunSyncHandler,
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status');

        if (
            $status !== null &&
            !SyncProcessStatus::tryFrom($status)
        ) {
            $status = null;
        }

        $context = $request->input('context');

        $step = $request->input('step');

        if (
            $step !== null &&
            !SyncProcessStep::tryFrom($step)
        ) {
            $step = null;
        }

        $sourceId = $request->input('source_id');

        $query = SyncProcess::query();

        $processes = (clone $query)
            ->when(
                $status,
                fn($query) => $query->where('status', $status)
            )
            ->when(
                $context,
                fn($query) => $query->where('context', $context)
            )
            ->when(
                $step,
                fn($query) => $query->where('current_step', $step)
            )
            ->when(
                $sourceId,
                fn($query) => $query->where('source_id', $sourceId)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = (clone $query)
            ->when(
                $context,
                fn($query) => $query->where('context', $context)
            )
            ->when(
                $step,
                fn($query) => $query->where('current_step', $step)
            )
            ->when(
                $sourceId,
                fn($query) => $query->where('source_id', $sourceId)
            )
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        $statusCounts['all'] = array_sum($statusCounts);

        $statuses = SyncProcessStatus::cases();

        $steps = SyncProcessStep::cases();

        $contexts = SyncProcess::query()
            ->select('context')
            ->distinct()
            ->orderBy('context')
            ->pluck('context');

        return view('synchub.index', compact(
            'processes',
            'statuses',
            'steps',
            'contexts',
            'status',
            'step',
            'context',
            'sourceId',
            'statusCounts',
        ));
    }

    public function show(int $id)
    {
        $process = SyncProcess::with([
            'logs',
            'dependencies.dependencyProcess',
            'parentRelations.parentProcess',
        ])->findOrFail($id);

        $triggeredProcesses = $process->triggeredRelations()
            ->with('childProcess')
            ->latest()
            ->paginate(
                5,
                ['*'],
                'triggered_page'
            );

        return view(
            'synchub.show',
            compact(
                'process',
                'triggeredProcesses'
            )
        );
    }

    public function status(int $id)
    {
        $process = SyncProcess::with('logs')
            ->findOrFail($id);

        return response()->json([
            'id' => $process->id,

            'status' => $process->status?->value,

            'status_label' => $process->status?->label(),

            'current_step' => [
                'value' => $process->current_step?->value,
                'label' => $process->current_step?->label(),
            ],

            'duration_seconds' => $process->started_at && $process->finished_at
                ? $process->started_at->diffInSeconds($process->finished_at)
                : null,

            'finished' => in_array(
                $process->status?->value,
                ['success', 'failed', 'error', 'obsolete'],
                true
            ),

            'logs' => $process->logs
                ->sortBy('created_at')
                ->values()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'message' => $log->message,
                        'created_at' => $log->created_at?->format('d/m/Y H:i:s'),
                        'payload' => $log->payload,
                    ];
                }),
        ]);
    }

    public function rerun(int $id)
    {
        $process = SyncProcess::findOrFail($id);

        abort_if(
            in_array(
                $process->status->value,
                [
                    'processing',
                    'pending',
                    'waiting_dependency',
                ]
            ),
            422,
            'Processo ainda em execução.'
        );

        $rerun = $this->rerunSyncHandler->handle($process->id);

        return redirect()->route(
            'synchub.show',
            $rerun->id,
        );
    }

    public function sync(
        Request $request,
        string $context,
    ) {
        abort_unless(
            Sync::has($context),
            404
        );

        $request->validate([
            'id' => 'required',
            'force' => 'boolean',
        ]);

        $this->startSyncHandler->handle(
            new StartSync(
                context: $context,
                sourceId: $request->integer('id'),
                force: $request->boolean('force'),
            )
        );

        return response()->json([
            'status' => 'queued',
        ]);
    }

    public function batch(
        Request $request,
        string $context,
    ) {

        abort_unless(
            Sync::has($context),
            404
        );

        $request->validate([
            'ids' => 'required|array',
            'force' => 'boolean',
        ]);

        $this->startBatchSyncHandler->handle(
            new StartBatchSync(
                context: $context,
                ids: $request->ids,
                force: $request->boolean('force'),
                chunkSize: 100,
            )
        );

        return response()->json([
            'status' => 'queued',
            'total' => count($request->ids),
        ]);
    }
}
