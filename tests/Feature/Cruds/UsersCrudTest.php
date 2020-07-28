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

}
