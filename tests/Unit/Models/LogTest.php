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
        $this->createPermissions();
        $this->addPermissionsForAdmin();
        $this->user = User::factory()->create();
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
            ->assertSee('filter-name="user_id"', false)
            ->assertSee('filter-name="route"', false)
            ->assertSee('filter-name="entity_type"', false)
            ->assertSee('filter-name="entity_id"', false)
            ->assertSee('filter-name="event"', false)
            ->assertSee('filter-name="created_at"', false)
            ->assertSee('filter-name="ip_address"', false);
    }

}
