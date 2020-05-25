<?php

namespace Different\Dwfw\app\Http\Controllers;

use Different\Dwfw\app\Models\TimeZone;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimeZones extends Controller
{
    /**
     * Sets timezone to session from time offset
     * @param Request $request
     * @return TimeZone|null
     */
    public function set(Request $request)
    {
        $offset = $request->post('timezone_offset_minutes');
        $prefix = $offset < 0 ? '-' : '+';
        $timezone_diff = $prefix . gmdate("H:i", $offset * 60);

        $timezone = TimeZone::getTimezoneByDiff($timezone_diff, 'Europe');
        if ($timezone) {
            $request->session()->put('timezone', $timezone);
        }

        return $timezone;
    }
}
