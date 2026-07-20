<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_lines', function (Blueprint $table): void {
            $table->string('status', 32)->default('confirmed')->after('transaction_reference');
            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->index(['transaction_reference', 'status']);
        });

        Schema::create('payment_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 64);
            $table->string('provider_event_id', 191);
            $table->string('event_type', 64);
            $table->string('transaction_reference', 191)->nullable();
            $table->json('payload');
            $table->string('processing_status', 32)->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_event_id']);
            $table->index(['transaction_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');

        Schema::table('payment_lines', function (Blueprint $table): void {
            $table->dropIndex(['transaction_reference', 'status']);
            $table->dropColumn(['status', 'confirmed_at']);
        });
    }
};
