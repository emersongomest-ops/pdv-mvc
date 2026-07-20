<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_records', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 128);
            $table->string('scope', 191);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('request_hash', 64);
            $table->string('request_id', 128)->nullable();
            $table->string('status', 20);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamps();

            $table->unique(['scope', 'key']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_records');
    }
};
