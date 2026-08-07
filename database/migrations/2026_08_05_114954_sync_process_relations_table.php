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
        Schema::create('sync_process_relations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('parent_process_id')
                ->constrained('sync_processes')
                ->cascadeOnDelete();

            $table->foreignId('child_process_id')
                ->constrained('sync_processes')
                ->cascadeOnDelete();

            $table->string('type', 30);

            $table->timestamps();

            $table->unique([
                'parent_process_id',
                'child_process_id',
                'type'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_process_relations');
    }
};
