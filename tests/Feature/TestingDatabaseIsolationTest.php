<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TestingDatabaseIsolationTest extends TestCase
{
    public function test_the_test_runner_uses_only_the_dedicated_database(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('web_onlinefea_test', config('database.connections.mysql.database'));
        $this->assertSame('web_onlinefea_test', DB::connection()->getDatabaseName());
    }
}
