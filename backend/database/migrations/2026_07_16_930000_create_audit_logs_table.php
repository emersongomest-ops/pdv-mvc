<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table): void {
                $table->id();
                $table->string('action', 64);
                $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
                $table->foreignId('store_id')->nullable()->constrained()->restrictOnDelete();
                $table->string('subject_type', 64);
                $table->unsignedBigInteger('subject_id');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('occurred_at')->useCurrent();

                $table->index(['action', 'occurred_at']);
                $table->index(['actor_user_id', 'occurred_at']);
                $table->index(['store_id', 'occurred_at']);
                $table->index(['subject_type', 'subject_id']);
                $table->index(['occurred_at', 'id']);
            });
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
            DB::unprepared(<<<'SQL'
CREATE TRIGGER audit_logs_no_update
BEFORE UPDATE ON audit_logs
BEGIN
    SELECT RAISE(ABORT, 'audit_logs are immutable');
END;
SQL);
            DB::unprepared(<<<'SQL'
CREATE TRIGGER audit_logs_no_delete
BEFORE DELETE ON audit_logs
BEGIN
    SELECT RAISE(ABORT, 'audit_logs are immutable');
END;
SQL);
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
            DB::unprepared(<<<'SQL'
CREATE TRIGGER audit_logs_no_update
BEFORE UPDATE ON audit_logs
FOR EACH ROW
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs are immutable';
SQL);
            DB::unprepared(<<<'SQL'
CREATE TRIGGER audit_logs_no_delete
BEFORE DELETE ON audit_logs
FOR EACH ROW
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs are immutable';
SQL);
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
        }

        Schema::dropIfExists('audit_logs');
    }
};
