<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class PermissionTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
        $this->createPermissions();
//        $this->addPermissionsForAdmin();
    }

    /** @test */
    public function loginRequiresPermission()
    {
        $this
            ->actingAs($this->user_admin)
            ->get(route('backpack.dashboard'))
            ->assertStatus(302);
    }

    /**
     * @test
     * @dataProvider permission_test_values
     */
    function accessDeniedIfUserDoesntHaveRequiredPermissions($route)
    {
        $this->givePermissions('admin',['login backend']);
        $this->actingAs($this->user_admin)->get(route($route))->assertStatus(403);
    }

    /**
     * @test
     * @dataProvider permission_test_values
     */
    function accessAllowedIfUserHaveRequiredPermissions($route, $permission)
    {
        $this->givePermissions('admin',['login backend', $permission]);
        $this->actingAs($this->user_admin)->get(route($route))->assertStatus(200);
    }

    function permission_test_values()
    {
        return [
            ['setting.index', 'manage settings',],
            ['admin./users.index', 'manage users',],
            ['admin./spammers.index', 'manage bans',],
            ['admin./logs.index', 'view logs'],
        ];
    }

    private function givePermissions(string $role, array $permissions)
    {
        foreach($permissions as $permission){
            Role::query()->where('name', $role)->first()->givePermissionTo($permission);
        }
    }
}

