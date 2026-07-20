<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Promotions\ValueObjects\DiscountType;
use App\Domain\Promotions\ValueObjects\StackingMode;
use App\Models\Customer;
use App\Models\Promotion;
use App\Support\Pii\PiiCrypto;
use Illuminate\Database\Seeder;

class DemoPromotionSeeder extends Seeder
{
    public function run(): void
    {
        Promotion::query()->firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'name' => 'Welcome 10% off',
                'discount_type' => DiscountType::Percent,
                'discount_value' => 1000,
                'stacking_mode' => StackingMode::Accumulable,
                'applies_to_all_customers' => true,
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addYear(),
            ],
        );

        $vip = Promotion::query()->firstOrCreate(
            ['code' => 'VIP5OFF'],
            [
                'name' => 'VIP R$5 off',
                'discount_type' => DiscountType::Fixed,
                'discount_value' => 500,
                'stacking_mode' => StackingMode::Unique,
                'applies_to_all_customers' => false,
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addYear(),
            ],
        );

        $maria = Customer::query()
            ->where('cpf_hash', PiiCrypto::blindIndex('39053344705'))
            ->first();

        if ($maria !== null) {
            $vip->customers()->syncWithoutDetaching([$maria->id]);
        }
    }
}
