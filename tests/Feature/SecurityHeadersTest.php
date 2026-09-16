<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_include_baseline_security_headers(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
    }

    public function test_strict_transport_security_is_only_sent_in_production(): void
    {
        $this->get(route('home'))->assertHeaderMissing('Strict-Transport-Security');

        $this->app->detectEnvironment(fn () => 'production');

        $this->get(route('home'))->assertHeader('Strict-Transport-Security');
    }
}
