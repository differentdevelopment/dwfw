<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;
use Request;

class LogTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
    }

    /** @test */
    function it_returns_user_name()
    {
        $user = factory(User::class)->create();
        $log = Log::create([
            'user_id' => $user->id,
            'route' => 'admin',
            'entity_type' => LOG::ET_SYSTEM,
            'event' => LOG::E_CREATED,
            'ip_address' => Request::ip(),
        ]);
        $this->assertEquals($user->username, $log->user_name);
    }

    /** @test */
    function filters_exists()
    {
        $this
            ->actingAs($this->user_admin)
            ->get(route('admin./logs.index'))
            ->assertSee('filter-name="userId"', false)
            ->assertSee('filter-name="route"', false)
            ->assertSee('filter-name="entityType"', false)
            ->assertSee('filter-name="entityId"', false)
            ->assertSee('filter-name="event"', false)
            ->assertSee('filter-name="createdAt"', false);
    }

}
