<?php

use Different\Dwfw\Tests\TestCase;

class MenuTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAdminAndGuestUser();
    }

    /** @test */
    public function testRegisterLocalization()
    {
        $this
            ->get(route('register'))
            ->assertSee(__('Register'))
            ->assertSee(__('Name'))
            ->assertSee(__('E-Mail Address'))
            ->assertSee(__('Confirm Password'))
            ->assertSee(__('Password'));
    }

    /** @test */
    public function testLoginLocalization()
    {
        $this
            ->get(route('login'))
            ->assertSee(__('E-Mail Address'))
            ->assertSee(__('Remember Me'))
            ->assertSee(__('Password'))
            ->assertSee(__('Forgot Your Password?'))
            ->assertSee(__('Login'));
    }

    /** @test */
    public function testPasswordResetLocalization()
    {
        $this
            ->get(route('password.request'))
            ->assertSee(__('Send Password Reset Link'))
            ->assertSee(__('E-Mail Address'));
    }

}
