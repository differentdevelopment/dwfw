<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\Tests\TestCase;

class UserTest extends TestCase
{

    /** @test */
    function it_verfies_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null,]);
        $user->verify();
        $this->assertNotNull($user->email_verified_at);
    }

}
