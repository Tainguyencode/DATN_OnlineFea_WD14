<?php

namespace Tests\Feature;

use Tests\TestCase;

class DevelopmentRouteSecurityTest extends TestCase
{
    public function test_development_helpers_are_not_registered_outside_local_environment(): void
    {
        $this->assertTrue(app()->environment('testing'));

        $this->get('/dev/login-as-admin')->assertNotFound();
        $this->get('/dev/login-as-student')->assertNotFound();
        $this->get('/test-frame')->assertNotFound();
        $this->get('/test-gemini')->assertNotFound();
        $this->post('/quick-login/admin')->assertNotFound();
    }
}
