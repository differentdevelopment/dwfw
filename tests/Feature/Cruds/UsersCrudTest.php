<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class UsersCrudTest extends TestCase
{
    private $user_admin;
    private $role_admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_admin = factory(User::class)->create();
        $this->role_admin = Role::query()->create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $this->user_admin->assignRole($this->role_admin->name);
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
}
