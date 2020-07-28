<?php

namespace Different\Dwfw\Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Cruds\MenuTest;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected static $database_built = false;
    protected $role_admin;
    /** @var User user_admin */
    protected $user_admin;
    protected $user_not_admin;

    protected function createAdminAndGuestUser(): void
    {
        $this->user_admin = factory(User::class)->create(['name' => 'Admin József']);
        $this->user_not_admin = factory(User::class)->create(['name' => 'Teszt Elemér']);
        $this->role_admin = Role::query()->create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $this->user_admin->assignRole($this->role_admin->name);
    }

}
