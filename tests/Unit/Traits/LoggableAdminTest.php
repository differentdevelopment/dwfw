<?php


namespace Different\Dwfw\Tests\Unit\Traits;

use App\Models\User;
use Different\Dwfw\app\Traits\LoggableAdmin;
use Different\Dwfw\Tests\TestCase;

class LoggableAdminTest extends TestCase
{
    use LoggableAdmin;

    /** @test */
    public function it_handles_array_on_data()
    {
        $this->actingAs($user = User::factory()->create());

        $log = $this->log('test_event', 1, ['foo' => 'bar']);

        $this->assertJson($log->data);
        $this->assertEquals($log->data, json_encode(['foo' => 'bar']));

        $log = $this->log('test_event', 1, (object)['foo' => 'bar']);

        $this->assertJson($log->data);
        $this->assertEquals($log->data, json_encode((object)['foo' => 'bar']));
    }
}


