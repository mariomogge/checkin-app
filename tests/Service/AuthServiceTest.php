<?php

use PHPUnit\Framework\TestCase;
use App\Service\AuthService;

final class AuthServiceTest extends TestCase
{
    private AuthService $auth;

    protected function setUp(): void
    {
        $this->auth = new AuthService();
    }

    public function testLoginFailsWithInvalidUser(): void
    {
        $this->expectException(Exception::class);
        $this->auth->login('nonexistent', 'invalid');
    }

    public function testTokenContainsExpectedFields(): void
    {
        $token = $this->auth->login('admin', 'admin'); // Beispiel-User
        $parts = explode('.', $token);
        $payload = json_decode(base64_decode($parts[1]), true);

        $this->assertArrayHasKey('sub', $payload);
        $this->assertArrayHasKey('role', $payload);
    }
}
