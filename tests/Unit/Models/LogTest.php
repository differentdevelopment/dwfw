<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;
use Request;

class LogTest extends TestCase
{
    protected Log $log;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
        $this->user = factory(User::class)->create();
        $this->log = Log::create([
            'user_id' => $this->user->id,
            'route' => 'admin',
            'entity_type' => 'App\Models\User',
            'entity_id' => $this->user->id,
            'event' => LOG::E_CREATED,
            'ip_address' => Request::ip(),
            'status' => 'OK',
        ]);
    }

    /** @test */
    function it_returns_user_name()
    {
        $this->assertEquals($this->user->username, $this->log->user_name);
    }

    /** @test */
    function it_returns_status()
    {
        $this->assertEquals('OK', $this->log->status);
    }

    /** @test */
    function it_returns_entity()
    {
        $this->assertEquals($this->user->id, $this->log->entity->id);
        $this->assertEquals($this->user->email, $this->log->entity->email);
        $this->assertEquals($this->user->name, $this->log->entity->name);
    }

    /** @test */
    function it_returns_log_name()
    {
        $this->assertEquals($this->log->log_name, $this->user->logName());
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
            ->assertSee('filter-name="createdAt"', false)
            ->assertSee('filter-name="status"', false);
    }

}
