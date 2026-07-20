<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_store_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('purchase_count')->default(0);
            $table->decimal('total_spend', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['customer_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_store_stats');
    }
};
