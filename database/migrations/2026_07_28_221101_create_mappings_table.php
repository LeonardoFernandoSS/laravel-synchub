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

            $table->string('source_type');
            $table->unsignedBigInteger('source_id');

            $table->string('target_id');

            $table->string('payload_hash', 64);

            $table->timestamps();

            $table->unique(
                ['source_type', 'source_id'],
                'sync_mappings_source_target_unique'
            );

            $table->index(
                ['target_id'],
                'sync_mappings_target_index'
            );

            $table->index(
                ['source_type', 'source_id'],
                'sync_mappings_source_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_mappings');
    }
};
