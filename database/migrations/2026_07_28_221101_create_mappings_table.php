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
        Schema::create('mappings', function (Blueprint $table) {
            $table->id();

            $table->morphs('mappable');

            $table->string('external_id');
            $table->string('external_system', 32); // Aumentado o tamanho para maior flexibilidade de nomes de APIs

            $table->string('payload_hash', 64);
            $table->timestamps();

            $table->unique(['mappable_type', 'mappable_id', 'external_system'], 'mappings_system_unique');

            $table->index(['external_system', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mappings');
    }
};
