<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_documentation_ui_is_available_locally(): void
    {
        Gate::define('viewApiDocs', fn (?User $user): bool => true);

        $this->get('/docs/api')
            ->assertOk()
            ->assertSee('Ecommerce Cost Tracker API');
    }

    public function test_openapi_document_describes_routes_requests_and_jwt_security(): void
    {
        Gate::define('viewApiDocs', fn (?User $user): bool => true);

        $document = $this->getJson('/docs/api.json')
            ->assertOk()
            ->json();

        $this->assertSame('3.1.0', $document['openapi']);
        $this->assertSame('Ecommerce Cost Tracker API', $document['info']['title']);
        $this->assertSame('bearer', $document['components']['securitySchemes']['http']['scheme']);
        $this->assertSame([], $document['paths']['/auth/login']['post']['security']);
        $this->assertSame([['http' => []]], $document['security']);
        $this->assertArrayHasKey('401', $document['paths']['/products']['get']['responses']);
        $this->assertSame([
            'product_id',
            'transaction_date',
            'quantity',
            'unit_cost',
        ], $document['components']['schemas']['StorePurchaseRequest']['required']);
    }
}
