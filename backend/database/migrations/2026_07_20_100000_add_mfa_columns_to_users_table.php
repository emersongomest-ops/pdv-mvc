<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->text('mfa_secret')->nullable()->after('remember_token');
            $table->timestamp('mfa_confirmed_at')->nullable()->after('mfa_secret');
            $table->unsignedBigInteger('mfa_last_otp_timestamp')->nullable()->after('mfa_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['mfa_secret', 'mfa_confirmed_at', 'mfa_last_otp_timestamp']);
        });
    }
};
