<?php

namespace Different\Dwfw\app\Models\Traits;

use Carbon\Carbon;
use Different\Dwfw\app\Models\TimeZone;

trait BaseDwfwTrait
{
    /**
     * @param array $data_array incoming array with lang keys
     * @return array localized array
     */
    public static function localizeArray(array $data_array): array
    {
        $return = [];
        foreach ($data_array as $key) {
            $return[$key] = __('admin.' . $key);
        }
        return $return;
    }

    /**
     * Updates models latlng coordinates from address json
     * @return bool
     */
    public function setCoordinatesFromAddress(): bool
    {
        if (!$this->address) {
            return true;
        }
        $this->latitude = $this->address['latlng']['lat'];
        $this->longitude = $this->address['latlng']['lng'];
        return $this->save();
    }

    /**
     * @param string $date
     * @return Carbon|null
     */
    public function createUtcFromLocalDate(string $date = null): ?Carbon
    {
        if (!$date) {
            return null;
        }
        $timezone = request()->get('timezone_id') ? TimeZone::query()->find(request()->get('timezone_id')) : $this->timezone;
        return Carbon::createFromFormat('Y-m-d H:i:s', $date, $timezone->name)->utc();
    }

    public function verify()
    {
        $this->update(['email_verified_at' => Carbon::now()]);
    }
}
