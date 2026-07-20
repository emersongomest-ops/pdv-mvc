<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Support;

use Illuminate\Contracts\Session\Session;

final class MfaPendingSession
{
    public const KEY = 'mfa.pending_user_id';

    public function put(Session $session, int $userId): void
    {
        $session->put(self::KEY, $userId);
    }

    public function userId(Session $session): ?int
    {
        $value = $session->get(self::KEY);

        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : null);
    }

    public function forget(Session $session): void
    {
        $session->forget(self::KEY);
    }
}
