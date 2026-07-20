<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Actions;

use App\Domain\Audit\DTOs\AuditLogEntry;
use App\Domain\Audit\Repositories\AuditLogRepositoryInterface;
use App\Domain\Audit\ValueObjects\AuditAction;
use App\Domain\IdentityAccess\Exceptions\AuthenticationDomainException;
use App\Domain\IdentityAccess\Repositories\UsersRepositoryInterface;
use App\Domain\Shared\ErrorCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Break-glass: clear another manager's TOTP + recovery so they re-enroll (RN-074).
 */
final class ResetManagerMfaAction
{
    public function __construct(
        private readonly UsersRepositoryInterface $users,
        private readonly AuditLogRepositoryInterface $auditLogs,
    ) {}

    public function execute(int $targetUserId, User $actor, string $reason): User
    {
        if ($targetUserId === (int) $actor->id) {
            throw new AuthenticationDomainException(ErrorCode::AuthCannotResetOwnMfa);
        }

        $target = $this->users->findById($targetUserId);

        if ($target === null) {
            throw new AuthenticationDomainException(ErrorCode::AuthUserNotFound);
        }

        if (! $target->isManager()) {
            throw new AuthenticationDomainException(ErrorCode::AuthMfaResetNotApplicable);
        }

        $hadMfa = $target->hasMfaEnabled();
        $hadSecret = is_string($target->mfa_secret) && $target->mfa_secret !== '';

        return DB::transaction(function () use ($target, $actor, $reason, $hadMfa, $hadSecret): User {
            $target->forceFill([
                'mfa_secret' => null,
                'mfa_confirmed_at' => null,
                'mfa_last_otp_timestamp' => null,
                'mfa_recovery_codes' => null,
            ])->save();

            DB::table('sessions')->where('user_id', $target->id)->delete();

            $this->auditLogs->append(new AuditLogEntry(
                action: AuditAction::ManagerMfaReset,
                actorUserId: (int) $actor->id,
                subjectType: 'user',
                subjectId: (int) $target->id,
                storeId: null,
                oldValues: [
                    'mfa_enabled' => $hadMfa,
                    'mfa_secret_present' => $hadSecret,
                ],
                newValues: [
                    'mfa_enabled' => false,
                    'mfa_secret_present' => false,
                ],
                metadata: [
                    'reason' => $reason,
                    'target_email' => $target->email,
                ],
            ));

            return $target->fresh(['stores']) ?? $target;
        });
    }
}
