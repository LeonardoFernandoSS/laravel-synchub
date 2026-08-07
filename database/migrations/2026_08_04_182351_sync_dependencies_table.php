<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sync_dependencies', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sync_process_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('depends_on_process_id')
                ->nullable()
                ->constrained('sync_processes')
                ->nullOnDelete();

            $table->string('context');
            $table->unsignedBigInteger('context_id');

            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();

            $table->unique([
                'sync_process_id',
                'context',
                'context_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_dependencies');
    }
};
