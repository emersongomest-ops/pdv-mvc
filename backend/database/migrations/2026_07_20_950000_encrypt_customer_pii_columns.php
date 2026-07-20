<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Widen PII columns for ciphertext + add HMAC blind indexes.
 * Avoids table rename (breaks SQLite FKs from sales / customer_store_stats).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (! Schema::hasColumn('customers', 'cpf_hash')) {
                $table->string('cpf_hash', 64)->nullable()->unique();
            }
            if (! Schema::hasColumn('customers', 'email_hash')) {
                $table->string('email_hash', 64)->nullable()->unique();
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::table('customers', function (Blueprint $table): void {
                $table->dropUnique(['cpf']);
            });

            DB::statement('ALTER TABLE customers MODIFY cpf TEXT NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY email TEXT NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY phone TEXT NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY address TEXT NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY birth_date TEXT NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            if (Schema::hasColumn('customers', 'cpf_hash')) {
                $table->dropUnique(['cpf_hash']);
                $table->dropColumn('cpf_hash');
            }
            if (Schema::hasColumn('customers', 'email_hash')) {
                $table->dropUnique(['email_hash']);
                $table->dropColumn('email_hash');
            }
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE customers MODIFY cpf VARCHAR(11) NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY email VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY phone VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY address VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE customers MODIFY birth_date DATE NOT NULL');

            Schema::table('customers', function (Blueprint $table): void {
                $table->unique('cpf');
            });
        }
    }
};
