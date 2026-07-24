<?php

namespace Tests;

use App\Http\Middleware\SingleSessionMiddleware;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // actingAs() không tạo ActiveSession như login thật; middleware này
        // sẽ logout và trả 401 JSON cho hầu hết feature test.
        $this->withoutMiddleware(SingleSessionMiddleware::class);
    }
}
