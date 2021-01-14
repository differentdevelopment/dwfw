<?php

namespace Different\Dwfw\tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;

class PartnerObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    function it_logs_created_partner()
    {
        $partner = $this->createPartner();
        $logs = Log::query()
            ->where([
                'event' => LOG::E_CREATED,
                'entity_type' => Log::ET_PARTNER,
                'entity_id' => $partner->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

    /** @test */
    function it_logs_updated_partner()
    {
        $partner = $this->createPartner();
        $partner->name = 'Foo Bar';
        $partner->save();
        $logs = Log::query()
            ->where([
                'event' => LOG::E_UPDATED,
                'entity_type' => Log::ET_PARTNER,
                'entity_id' => $partner->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

    /** @test */
    function it_logs_deleted_partner()
    {
        $partner = $this->createPartner();
        $partner->delete();
        $logs = Log::query()
            ->where([
                'event' => LOG::E_DELETED,
                'entity_type' => Log::ET_PARTNER,
                'entity_id' => $partner->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

}
