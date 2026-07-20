<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Security;

use App\Support\Security\ProductionSecurityConfigAssertor;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class ProductionSecurityConfigAssertorTest extends TestCase
{
    #[Test]
    public function non_production_environments_are_ignored(): void
    {
        config(['app.debug' => true, 'session.secure' => false]);

        (new ProductionSecurityConfigAssertor)->assertForEnvironment('local');
        (new ProductionSecurityConfigAssertor)->assertForEnvironment('testing');

        $this->assertTrue(true);
    }

    #[Test]
    public function production_rejects_debug_enabled(): void
    {
        config(['app.debug' => true, 'session.secure' => true]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('APP_DEBUG must be false');

        (new ProductionSecurityConfigAssertor)->assertForEnvironment('production');
    }

    #[Test]
    public function production_rejects_insecure_session_cookie(): void
    {
        config(['app.debug' => false, 'session.secure' => false]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('SESSION_SECURE_COOKIE must be true');

        (new ProductionSecurityConfigAssertor)->assertForEnvironment('production');
    }

    #[Test]
    public function production_accepts_hardened_config(): void
    {
        config(['app.debug' => false, 'session.secure' => true]);

        (new ProductionSecurityConfigAssertor)->assertForEnvironment('production');

        $this->assertTrue(true);
    }
}
