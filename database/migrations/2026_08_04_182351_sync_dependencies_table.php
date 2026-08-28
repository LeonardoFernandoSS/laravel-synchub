<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_dependencies', function (Blueprint $table) {
            $table->id();

            // Processo que está aguardando a dependência
            $table->foreignId('sync_process_id')
                ->constrained('sync_processes')
                ->cascadeOnDelete();

            // Processo que precisa ser concluído
            $table->foreignId('depends_on_process_id')
                ->nullable()
                ->constrained('sync_processes')
                ->nullOnDelete();

            // Entidade que originou a dependência
            $table->string('context');
            $table->string('source_key', 500);

            // Estado da dependência
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'sync_process_id',
                    'context',
                    'source_id',
                ],
                'sync_dependencies_process_context_unique'
            );

            $table->index(
                ['sync_process_id', 'resolved'],
                'sync_dependencies_process_resolved_index'
            );

            $table->index(
                ['depends_on_process_id', 'resolved'],
                'sync_dependencies_dependency_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_dependencies');
    }
};