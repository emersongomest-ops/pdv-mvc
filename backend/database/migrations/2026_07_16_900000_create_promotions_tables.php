<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->string('discount_type');
            $table->decimal('discount_value', 12, 2);
            $table->string('stacking_mode');
            $table->boolean('applies_to_all_customers')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_promotion', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['promotion_id', 'customer_id']);
        });

        Schema::create('sale_promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['sale_id', 'promotion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_promotions');
        Schema::dropIfExists('customer_promotion');
        Schema::dropIfExists('promotions');
    }
};
