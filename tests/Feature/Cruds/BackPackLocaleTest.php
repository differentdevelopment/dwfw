<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class BackPackLocaleTest extends TestCase
{

    private $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
        $this->createPermissions();
        $this->addPermissionsForAdmin();
        $this->locale = App::getLocale();
    }

    /** @test */
    function testCrudLocalization()
    {
        $this
            ->actingAs($this->user_admin)
            ->get(route('admin.partners.index'))
            ->assertSee(trans('backpack::crud.reset'))
            ->assertSee(trans('backpack::crud.actions'));

        if($this->locale !== 'en'){
            $this->actingAs($this->user_admin)
                ->get(route('admin.partners.index'))
                ->assertDontSee('Actions');
        }
    }

    /** @test */
    function testBaseLocalization()
    {
        $this->actingAs($this->user_admin)
            ->get(route('backpack.account.info'))
            ->assertSee(trans('backpack::base.change_password'))
            ->assertSee(trans('backpack::base.update_account_info'));

        if($this->locale !== 'en'){
            $this->actingAs($this->user_admin)
                ->get(route('backpack.account.info'))
                ->assertDontSee('My Account')
                ->assertDontSee('Change password');
        }

    }

    /** @test */
    function testPermissionManagerLocalization(){
        $this->actingAs($this->user_admin)
            ->get(route('users.edit', $this->user_not_admin))
            ->assertSee(trans('backpack::permissionmanager.user_role_permission'))
            ->assertSee(trans('backpack::permissionmanager.password_confirmation'));

        if($this->locale !== 'en'){
            $this->actingAs($this->user_admin)
                ->get(route('users.edit', $this->user_not_admin))
                ->assertDontSee('User Role Permissions')
                ->assertDontSee('Password');
        }
    }

    /** @test */
    function testSettingsLocalization(){
        $this->actingAs($this->user_admin)
            ->get(route('setting.index'))
            ->assertSee(trans('backpack::settings.value'))
            ->assertSee(trans('backpack::settings.description'));

        if($this->locale !== 'en'){
            $this->actingAs($this->user_admin)
                ->get(route('setting.index'))
                ->assertDontSee('Value')
                ->assertDontSee('Description');
        }
    }

}
