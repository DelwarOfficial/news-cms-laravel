<?php

namespace Tests\Feature\Monitoring;

use App\Models\Language;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RequestIdTest extends TestCase
{
    use DatabaseTransactions;

    public function test_api_responses_include_request_id_header(): void
    {
        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertOk();
        $this->assertNotEmpty($response->headers->get('X-Request-Id'));
    }

    public function test_request_id_is_preserved_when_provided(): void
    {
        $response = $this->withHeader('X-Request-Id', 'test-request-id')->get('/healthz');

        $response->assertOk();
        $this->assertSame('test-request-id', $response->headers->get('X-Request-Id'));
    }
}
