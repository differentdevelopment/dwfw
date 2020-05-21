<?php

namespace Different\Dwfw\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @param array $data_array incoming array with lang keys
     * @return array localized array
     */
    protected static function localizeArray(array $data_array): array
    {
        $return = [];
        foreach ($data_array as $key) {
            $return[$key] = __('admin.' . $key);
        }
        return $return;
    }

    /**
     * @param string $date
     * @return Carbon|null
     */
    protected function createUtcFromLocalDate(string $date = null): ?Carbon
    {
        if (!$date) {
            return null;
        }
        $timezone = request()->get('timezone_id') ? TimeZone::find(request()->get('timezone_id')) : $this->timezone;
        return Carbon::createFromFormat('Y-m-d H:i:s', $date, $timezone->name)->utc();
    }

}
