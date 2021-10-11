<?php

namespace Different\Dwfw\app\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

trait LocalizedCarbon
{
    public function getCreatedAtLocalizedAttribute()
    {
        return $this->getLocalizedDate($this->created_at);
    }

    public function getUpdatedAtLocalizedAttribute()
    {
        return $this->getLocalizedDate($this->updated_at);
    }

    public function getDeletedAtLocalizedAttribute()
    {
        return $this->getLocalizedDate($this->deleted_at);
    }

    public function getLocalizedDate($date, $format = '')
    {
        if (!$date) return '';
        if (!$format) $format = config('backpack.base.default_datetime_format');

        return Carbon::parse($date)
            ->locale(App::getLocale())
            ->isoFormat($format);
    }

}
