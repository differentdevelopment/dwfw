<?php

namespace Different\Dwfw\Tests\Unit\Models;

use App\Models\User;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;
use Request;

class LogTest extends TestCase
{

    /** @test */
    function it_returns_user_name()
    {
        $user = factory(User::class)->create();
        $log = Log::create([
            'user_id' => $user->id,
            'route' => 'admin',
            'entity_type' => LOG::ET_SYSTEM,
            'event' => LOG::E_CREATED,
            'ip_address' => Request::ip(),
        ]);
        $this->assertEquals($user->username, $log->user_name);
    }

}
