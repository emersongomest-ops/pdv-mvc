<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Convert monetary decimal columns to integer cents (and percent_scaled for promotions.discount_value).
 * Existing rows: ROUND(col * 100).
 */
return new class extends Migration
{
    /**
     * @var array<string, list<array{column: string, nullable?: bool, default?: int|null}>>
     */
    private array $columnsByTable = [
        'products' => [
            ['column' => 'base_price'],
        ],
        'sale_lines' => [
            ['column' => 'unit_price'],
            ['column' => 'line_discount', 'default' => 0],
            ['column' => 'line_total'],
        ],
        'sales' => [
            ['column' => 'subtotal', 'default' => 0],
            ['column' => 'discount_total', 'default' => 0],
            ['column' => 'total', 'default' => 0],
        ],
        'payment_lines' => [
            ['column' => 'amount'],
            ['column' => 'cash_received', 'nullable' => true],
            ['column' => 'change_amount', 'nullable' => true],
        ],
        'cash_shifts' => [
            ['column' => 'opening_cash_amount', 'default' => 0],
            ['column' => 'closing_cash_amount', 'nullable' => true],
        ],
        'promotions' => [
            ['column' => 'discount_value'],
        ],
        'sale_promotions' => [
            ['column' => 'discount_amount', 'default' => 0],
        ],
        'refunds' => [
            ['column' => 'amount'],
        ],
        'refund_lines' => [
            ['column' => 'amount'],
        ],
        'customers' => [
            ['column' => 'lifetime_spend', 'default' => 0],
        ],
        'customer_store_stats' => [
            ['column' => 'total_spend', 'default' => 0],
        ],
    ];

    public function up(): void
    {
        foreach ($this->columnsByTable as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $meta) {
                $column = $meta['column'];
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)->update([
                    $column => DB::raw("ROUND({$column} * 100)"),
                ]);
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
                foreach ($columns as $meta) {
                    $col = $blueprint->unsignedBigInteger($meta['column']);

                    if (($meta['nullable'] ?? false) === true) {
                        $col->nullable()->change();
                    } elseif (array_key_exists('default', $meta)) {
                        $col->default($meta['default'])->change();
                    } else {
                        $col->change();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->columnsByTable as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
                foreach ($columns as $meta) {
                    $col = $blueprint->decimal($meta['column'], 14, 2);

                    if (($meta['nullable'] ?? false) === true) {
                        $col->nullable()->change();
                    } elseif (array_key_exists('default', $meta)) {
                        $col->default(($meta['default'] ?? 0) / 100)->change();
                    } else {
                        $col->change();
                    }
                }
            });

            foreach ($columns as $meta) {
                $column = $meta['column'];
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)->update([
                    $column => DB::raw("ROUND({$column} / 100.0, 2)"),
                ]);
            }
        }
    }
};
