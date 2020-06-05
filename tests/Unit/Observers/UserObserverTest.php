<?php

namespace Different\Dwfw\tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;

class UserObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createUser());
    }

    /** @test */
    function it_logs_created_user()
    {
        $logs = Log::query()
            ->where([
                'event' => LOG::E_CREATED,
                'entity_type' => Log::ET_USER,
                'entity_id' => (factory(User::class)->create())->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

    /** @test */
    function it_logs_updated_user()
    {
        $user = factory(User::class)->create();
        $user->name = 'Foo Bar';
        $user->save();
        $logs = Log::query()
            ->where([
                'event' => LOG::E_UPDATED,
                'entity_type' => Log::ET_USER,
                'entity_id' => $user->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

    /** @test */
    function it_logs_deleted_user()
    {
        $user = factory(User::class)->create();
        $user->delete();
        $logs = Log::query()
            ->where([
                'event' => LOG::E_DELETED,
                'entity_type' => Log::ET_USER,
                'entity_id' => $user->id,
            ]);
        $this->assertEquals(1, $logs->count());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    protected function createUser()
    {
        return factory(User::class)->create();
    }

}
