<?php

namespace Tests\Feature\Monitoring;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HealthzTest extends TestCase
{
    use DatabaseTransactions;

    public function test_healthz_returns_ok(): void
    {
        $response = $this->get('/healthz');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonStructure(['checks', 'errors', 'request_id']);
    }
}
