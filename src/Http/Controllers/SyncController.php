<?php

namespace Synchub\LaravelSynchub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Synchub\LaravelSynchub\Application\Sync\Commands\ResumeSync;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\ResumeSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartBatchSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use Synchub\LaravelSynchub\Domain\Sync\Models\SyncProcess;

class SyncController extends Controller
{
    public function __construct(
        private StartSyncHandler $startSyncHandler,
        private StartBatchSyncHandler $startBatchSyncHandler,
        private ResumeSyncHandler $resumeSyncHandler,
    ) {}

    public function index()
    {
        $processes = SyncProcess::latest()
            ->paginate(15);

        return view(
            'sync-processes.index',
            compact('processes')
        );
    }

    public function show(int $id)
    {
        $process = SyncProcess::with([
            'logs',
            'dependencies.dependencyProcess',
            'triggeredRelations.childProcess',
            'parentRelations.parentProcess',
        ])
            ->findOrFail($id);


        return view(
            'sync-processes.show',
            compact('process')
        );
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

        $this->resumeSyncHandler->handle(
            new ResumeSync(
                $process->id
            )
        );

        return redirect()->route(
            'sync-processes.show',
            $process,
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
            'id' => 'required|integer',
            'force' => 'boolean',
        ]);

        $this->startSyncHandler->handle(
            new StartSync(
                context: $context,
                id: $request->integer('id'),
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
            'ids.*' => 'integer',
            'force' => 'boolean',
        ]);

        $this->startBatchSyncHandler->handle(
            new StartBatchSync(
                context: $context,
                ids: $request->ids,
                force: $request->boolean('force'),
            )
        );

        return response()->json([
            'status' => 'queued',
            'total' => count($request->ids),
        ]);
    }
}
