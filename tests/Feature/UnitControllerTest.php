<?php

namespace Tests\Feature;

use App\Http\Controllers\UnitController;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class UnitControllerTest extends TestCase
{
    public function test_units_endpoint_returns_error_message_when_query_fails(): void
    {
        Mockery::mock('alias:App\\Models\\Unit')
            ->shouldReceive('select')
            ->once()
            ->andThrow(new RuntimeException('Database unavailable'));

        $response = app(UnitController::class)->getUnits();

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'message' => 'Unable to load units at the moment. Please try again later.',
            'data' => [],
        ], $response->getData(true));
    }
}
