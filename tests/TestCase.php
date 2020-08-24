<?php

namespace Different\Dwfw\Tests;

use App\Models\User;
use Different\Dwfw\app\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

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

    /**
     * InvalidArgumentException : Unable to locate factory for [Different\Dwfw\app\Models\Partner].
     * @return Partner
     */
    protected function createPartner(): Partner
    {
        return Partner::query()->create([
            'name' => $this->faker->name,
            'contact_name' => $this->faker->name,
            'contact_phone' => $this->faker->phoneNumber,
            'contact_email' => $this->faker->unique()->safeEmail,
        ]);
    }

}
