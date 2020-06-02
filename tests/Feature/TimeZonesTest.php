<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TimeZonesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', [
            '--class' => 'Different\\Dwfw\\database\\seeds\\DwfwSeeder',
        ]);
    }

    /** @test */
    function timezone_set_by_seconds_offset()
    {
        $response = $this
            ->postJson(route('set-timezone'), [
                'timezone_offset_minutes' => 120,
            ]);
        $response->assertSessionHas('timezone');
    }
}
