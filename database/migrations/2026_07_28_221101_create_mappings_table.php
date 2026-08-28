<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_mappings', function (Blueprint $table) {
            $table->id();

            /*
             * Identidade da origem
             */
            $table->string('source_type', 150);
            $table->string('source_key', 500);
            $table->json('source_identity');

            /*
             * Identidade do destino
             */
            $table->string('target_key', 500);
            $table->json('target_identity');

            /*
             * Controle de alterações
             */
            $table->string('payload_hash', 64);
            $table->json('last_payload')->nullable();

            $table->timestamps();

            /*
             * Um mapping por identidade de origem.
             */
            $table->unique(
                ['source_type', 'source_key'],
                'sync_mappings_source_identity_unique'
            );

            /*
             * Busca pelo destino.
             */
            $table->index(
                ['target_type', 'target_key'],
                'sync_mappings_target_index'
            );

            /*
             * Busca pela origem.
             */
            $table->index(
                ['source_type', 'source_key'],
                'sync_mappings_source_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_mappings');
    }
};
