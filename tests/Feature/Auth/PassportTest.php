<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Different\Dwfw\Tests\TestCase;

class PassportTest extends TestCase
{

    const API_URL_REGISTER = '/api/v1/register';
    const API_URL_CONFIRM = '/api/v1/register-confirm';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->make([])->makeVisible('password');
        $this->artisan('passport:install');
    }

    /** @test */
    public function it_should_login() //Nem kell csodálkozni, hogy failed a test, ha nem állítottad be a passportot -> php artisan dwfw:install-passport
    {
        $password = $this->faker->word;
        /** @var User $user */
        $user = User::factory()->create(['password' => Hash::make($password)]);

        $foo = $this->postJson(
            '/api/v1/login',
            [
                'email' => $user->email,
                'password' => $password,
            ],
            ['Accept' => 'application/json']
        )
            ->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_at']);
    }

    /** @test */
    protected function it_should_have_required_fields($api_url = null, $required_fields = null, $api_data = null, $token = null)
    {
        parent::it_should_have_required_fields(self::API_URL_REGISTER, ['name', 'email'], $this->user->toArray() + ['password_confirmation' => $this->user->password]);
    }

    /** @test */
    public function it_should_register_a_new_user()
    {
        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ])
            ->assertStatus(200);
        $this->assertJson($response->getContent());
        $this->assertEquals(
            User::query()->where('email', $this->user->email)->firstOrFail()->name,
            $this->user->name
        );
    }

    /** @test */
    public function it_requires_a_valid_email()
    {
        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => 'invalid_email_address',
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /** @test
     * @throws Throwable
     */
    public function it_requires_unique_email()
    {
        $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ]);

        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /** @test
     * @throws Throwable
     */
    public function it_requires_a_password_equality()
    {
        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password . 'a',
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /** @test
     * @throws Throwable
     */
    public function it_requires_a_strong_password()
    {
        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => '12345',
                'password_confirmation' => '12345',
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /** @test
     * @throws Throwable
     */
    public function it_requires_a_normal_length_name()
    {
        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => 'a',
                'email' => $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('name', $response['message']);

        $response = $this
            ->postJson(self::API_URL_REGISTER, [
                'name' => 'bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
                'email' => 'x' . $this->user->email,
                'password' => $this->user->password,
                'password_confirmation' => $this->user->password,
            ])
            ->decodeResponseJson();
        $this->assertTrue($response['error']);
        $this->assertArrayHasKey('name', $response['message']);
    }
}

