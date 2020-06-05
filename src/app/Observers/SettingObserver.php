<?php

namespace Different\Dwfw\app\Observers;

use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Models\Log;

class SettingObserver extends Observer
{
    protected $ENTITY_TYPE = Log::ET_SETTING;

    /**
     * @param Setting $setting
     * @return string
     */
    public function getData($setting)
    {
        return $this->implodeData([
            $setting->name,
            $setting->value,
        ]);
    }

}
