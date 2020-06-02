<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_verfies_user()
    {
        $user = factory(User::class)->create(['email_verified_at' => null,]);
        $user->verify();
        $this->assertNotNull($user->email_verified_at);
    }
}
