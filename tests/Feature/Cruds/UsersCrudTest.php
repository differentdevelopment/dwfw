<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class UsersCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
    }

    /** @test */
    function user_grid_verify_button_exists()
    {
        $this
            ->actingAs($this->user_admin)
            ->postJson('/admin/users/search', [
                'draw' => 1,
            ])
            ->assertSee(str_replace('/', '\/', route('admin.verify', $this->user_admin)));
    }

    /** @test */
    function normal_user_unauthorized()
    {
        $this
            ->actingAs($this->user_not_admin)
            ->postJson('/admin/users/search')
            ->assertUnauthorized();
    }

    /** @test */
    function user_show_exists()
    {
        $this
            ->actingAs($this->user_admin)
            ->get('/admin/users/' . $this->user_not_admin->id . '/show')
            ->assertSee($this->user_not_admin->name)
            ->assertSee($this->user_not_admin->email)
            ->assertSee($this->user_not_admin->email_verified_at);
    }

}
