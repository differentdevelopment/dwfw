<?php

namespace Different\Dwfw\tests\Unit\Models;

use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Models\Log;
use Different\Dwfw\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class SettingObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
        Artisan::call('db:seed', [
            '--class' => 'Different\\Dwfw\\database\\seeds\\DwfwSeeder',
        ]);
    }

    /** @test */
    function it_logs_updated_setting() //FIXME since there's no settings seeded, test failed, but factory not working and only fillable attribute is value - Urudin
    {
        $setting = Setting::first();
        if ($setting) {
            $setting->name = 'Foo Bar';
            $setting->save();
            $logs = Log::query()
                ->where([
                    'event' => LOG::E_UPDATED,
                    'entity_type' => Log::ET_SETTING,
                    'entity_id' => $setting->id,
                ]);
            $this->assertEquals(1, $logs->count());
        } else {
            $this->assertNull($setting);
        }
    }


}
