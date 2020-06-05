<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;
use Illuminate\Testing\TestResponse;

class UsersCrudTest extends TestCase
{
    /** @var User user_admin */
    private $user_admin;
    private $user_not_admin;
    private $role_admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_admin = factory(User::class)->create(['name' => 'Admin József']);
        $this->user_not_admin = factory(User::class)->create(['name' => 'Teszt Elemér']);
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

    /** @test */
    function normal_user_unauthorized()
    {
        $this
            ->actingAs($this->user_not_admin)
            ->postJson('/admin/users/search')
            ->assertUnauthorized();
    }

    /** @test */
    function user_search()
    {
        // elfér..
        $res = $this->actingAs($this->user_admin)
            ->postJson('/admin/users/search?', [
                'search' => ['value' => 'Elemér'],              // tudunk ékezetre keresni?
            ]);
        $res->assertSeeText('Teszt Elem\u00e9r');       // kódoltan legyen benne az ékezet és legyen meg az emberünk
        $res->assertDontSeeText('Elemér');              // nem lehet kódolatlanul benne a név
        $res->assertDontSeeText('Admin J\u00f3zsef');        // nem lehet benne a másik felhasználó, ha egyszer Elemérre kerestünk!
        $res->assertSeeText('"recordsFiltered":1', false);       // valójában csak egyetlen találatot kell kapjunk
    }

    /** @test */
    function user_verified()
    {
        // alapból legyen null
        $this->assertNull($this->user_admin->email_verified_at);

        // ne működjön, ha nem admin nyomja
        /** @var TestResponse $res */
        $res = $this->actingAs($this->user_not_admin)->get(route('admin.verify', $this->user_admin));
//        $res->assertDontSeeText('"success"', false);

        $this->user_admin = User::query($this->user_admin)->first();

        // maradjon null
        $this->assertNull($this->user_admin->email_verified_at);

        // működjön, ha admin nyomja
        $res = $this->actingAs($this->user_admin)->get(route('admin.verify', $this->user_admin));
        //$res->assertSeeText('"success"', false);
//        $res->assertSessionHas('handler');
//        $res = str_replace('/', '\/', $res);
//        $res->assertSeeText('"success"', false);

        // meh..
        $this->user_admin = User::query($this->user_admin)->first();

        // és ne legyen null innentől
        $this->assertNotNull($this->user_admin->email_verified_at);
    }

//    /** @test */
//    function user_avatar_set_correctly()
//    {
//
//    }
}
