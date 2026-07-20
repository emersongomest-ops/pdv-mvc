<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private const TESTING_APP_KEY = 'base64:YWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWFhYWE=';

    protected function setUp(): void
    {
        // Docker injects empty APP_KEY via env_file; force before and after boot.
        putenv('APP_KEY='.self::TESTING_APP_KEY);
        $_ENV['APP_KEY'] = self::TESTING_APP_KEY;
        $_SERVER['APP_KEY'] = self::TESTING_APP_KEY;

        parent::setUp();

        config(['app.key' => self::TESTING_APP_KEY]);
    }
}
