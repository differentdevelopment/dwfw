<?php

namespace Different\Dwfw\Tests\Unit\Models;

use Different\Dwfw\app\Models\TimeZone;
use Different\Dwfw\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class TimeZoneTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', [
            '--class' => 'Different\\Dwfw\\database\\seeds\\DwfwSeeder',
        ]);
    }

    /**
     * @test
     * @dataProvider timezone_test_values
     */
    function it_return_timezone_name_by_time_difference($expected_name, $time_difference, $continent_prefix = null)
    {
        $time_zone = TimeZone::getTimezoneByDiff($time_difference, $continent_prefix);
        $this->assertEquals($time_difference, $time_zone->diff);
        $this->assertEquals($expected_name, $time_zone->name);
    }

    function timezone_test_values()
    {
        return [
            ['Africa/Blantyre', '+02:00',],
            ['America/Araguaina', '-03:00',],
            ['Pacific/Midway', '-11:00',],
            ['Europe/Amsterdam', '+01:00', 'Europe'],
            ['Europe/Athens', '+02:00', 'Europe'],
            ['Africa/Blantyre', '+02:00', 'Africa'],
        ];
    }

    /** @test */
    function it_returns_timezone_name_with_time_difference()
    {
        $this->assertEquals('My timezone (UTC+12:34)', (new TimeZone([
            'name' => 'My timezone',
            'diff' => '+12:34',
        ]))->name_with_diff);
        $this->assertEquals('My timezone2 (UTC-99:99)', (new TimeZone([
            'name' => 'My timezone2',
            'diff' => '-99:99',
        ]))->name_with_diff);
    }
}
