<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->decimal('amount', 12, 2);
            $table->decimal('cash_received', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamps();

            $table->index(['sale_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_lines');
    }
};
