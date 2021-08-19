<?php
namespace Different\Dwfw\app\Http\Services;

use Different\Dwfw\app\Models\Log;
use Backpack\Settings\app\Models\Setting;

class LogDeleteSchedule
{
    public function __invoke() :void
    {
        
        $this->delete('NOTICE');
        $this->delete('WARNING');       
    }
    private function delete($category)
    {
        $categories = json_decode(Setting::get($category.'_CATEGORY'),true); 
        if(empty($categories))
        {
            return;
        }               
        $day =Setting::get($category.'_TIME');        
        if ($day == null) {
            return;
        }           
        Log::query()->whereIn("event", $categories)->whereDate("created_at", '<=', now()->subDays($day))->delete();
    }
}