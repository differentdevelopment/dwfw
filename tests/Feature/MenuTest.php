<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class MenuTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
        $this->createPermissions();
        $this->addPermissionsForAdmin();
    }

    /** @test */
    public function testSidebarLocalization()
    {
        $this
            ->actingAs($this->user_admin)
            ->get(route('admin./users.index'))
            ->assertSee(__('dwfw::users.users'))
            ->assertSee(__('dwfw::settings.settings'))
            ->assertSee(__('dwfw::logs.logs'))
            ->assertSee(__('dwfw::partners.partners'));
    }

}

