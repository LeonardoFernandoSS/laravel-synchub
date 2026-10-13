<?php

namespace Synchub\LaravelSynchub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartBatchSync;
use Synchub\LaravelSynchub\Application\Sync\Commands\StartSync;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartBatchSyncHandler;
use Synchub\LaravelSynchub\Application\Sync\Handlers\StartSyncHandler;
use Synchub\LaravelSynchub\Domain\Sync\Facades\Sync;
use Synchub\LaravelSynchub\Domain\Sync\ValueObjects\SourceIdentity;

class SyncController extends Controller
{
    public function __construct(
        private StartSyncHandler $startSyncHandler,
        private StartBatchSyncHandler $startBatchSyncHandler,
    ) {}

    public function sync(
        Request $request,
        string $context,
    ) {
        abort_unless(
            Sync::has($context),
            404,
        );

        $validated = $request->validate([
            'source' => ['required'],
            'data' => ['sometimes', 'array'],
            'force' => ['boolean'],
        ]);

        $identity = Sync::identityResolver($context)
            ->resolve($validated['source']);

        $this->startSyncHandler->handle(
            new StartSync(
                context: $context,
                identity: $identity,
                force: $request->boolean('force'),
                sourcePayload: $validated['data'] ?? null,
            ),
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
            404,
        );

        $validated = $request->validate([
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*' => [
                'required',
                'array',
            ],
            'items.*.source' => [
                'required',
            ],
            'items.*.data' => [
                'sometimes',
                'array',
            ],
            'force' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $resolver = Sync::identityResolver($context);

        /** @var SourceIdentity[] */
        $identities = $resolver->resolveMany(
            array_column($validated['items'], 'source'),
        );

        $sourcePayloads = [];

        foreach ($identities as $index => $identity) {
            if (array_key_exists('data', $validated['items'][$index])) {
                $sourcePayloads[$identity->key()] =
                    $validated['items'][$index]['data'];
            }
        }

        $this->startBatchSyncHandler->handle(
            new StartBatchSync(
                context: $context,
                identities: $identities,
                force: $request->boolean('force'),
                chunkSize: 100,
                sourcePayloads: $sourcePayloads,
            ),
        );

        return response()->json([
            'status' => 'queued',
            'total' => count($identities),
        ]);
    }
}
