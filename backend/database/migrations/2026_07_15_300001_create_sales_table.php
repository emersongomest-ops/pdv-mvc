<?php

declare(strict_types=1);

use App\Domain\Sales\ValueObjects\SaleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_shift_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default(SaleStatus::InProgress->value);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'status']);
            $table->index(['cash_shift_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
