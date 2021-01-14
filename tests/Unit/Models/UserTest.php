<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\Tests\TestCase;

class UserTest extends TestCase
{

    /** @test */
    function it_verfies_user()
    {
        /** @var User $user */
        $user = User::factory()->create(['email_verified_at' => null,]);
        $this->assertNull($user->email_verified_at);
        $user->verify();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEmpty($user->profile_image);

        // ez valami privát cucc
//        $usr = User::query($user)->first();
//        $this->assertNotEmpty($usr->default_image);
//        $this->assertNotEmpty($usr->default_image_icon);
    }
}
