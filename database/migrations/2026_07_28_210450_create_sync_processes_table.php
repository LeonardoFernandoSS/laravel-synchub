<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_processes', function (Blueprint $table) {
            $table->id();

            /*
             * Identidade da sincronização
             */
            $table->string('context', 150);
            $table->string('source_id');

            /*
             * Estado
             */
            $table->string('status', 32)->default('pending');
            $table->string('current_step', 64);

            /*
             * Opções de execução
             */
            $table->boolean('force')->default(false);

            /*
             * Dados da sincronização
             */
            $table->json('source_payload')->nullable();
            $table->json('target_payload')->nullable();
            $table->json('target_response')->nullable();

            /*
             * Erro
             */
            $table->json('error')->nullable();

            /*
             * Cache do payload de origem
             */
            $table->timestamp('payload_cached_at')->nullable();

            /*
             * Controle do ciclo de vida
             */
            $table->timestamp('started_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            /*
             * Identidade da sincronização.
             */
            $table->index(
                ['context', 'source_id'],
                'sync_processes_identity_index'
            );

            /*
             * Busca de processos ativos.
             */
            $table->index(
                ['context', 'source_id', 'status'],
                'sync_processes_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_processes');
    }
};
