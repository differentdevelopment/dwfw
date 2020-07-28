<?php

namespace Tests\Feature\Cruds;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Different\Dwfw\Tests\TestCase;

class PartnersCrudTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
    }

    /** @test */
    function testSidebarLocalization()
    {

        $this
            ->actingAs($this->user_admin)
            ->get(route('admin.partners.index'))
            ->assertSee(__('dwfw::partners.name'))
            ->assertSee(__('dwfw::partners.contact_name'))
            ->assertSee(__('dwfw::partners.contact_phone'))
            ->assertSee(__('dwfw::partners.contact_email'));
    }

}
