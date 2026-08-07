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
        Schema::create('sync_processes', function (Blueprint $table) {

            $table->id();
            $table->string('type');
            $table->string('context');
            $table->unsignedBigInteger('context_id');
            $table->string('status')->default('pending');
            $table->string('current_step');
            $table->boolean('force')->default(false);
            $table->json('mapped_payload')->nullable();
            $table->timestamp('payload_cached_at')->nullable();
            $table->json('internal_payload')->nullable();
            $table->json('external_response')->nullable();
            $table->json('error')->nullable();          
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_processes');
    }
};
