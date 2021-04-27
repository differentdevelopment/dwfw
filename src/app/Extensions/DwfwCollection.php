<?php

namespace Different\Dwfw\app\Extensions;

use Illuminate\Database\Eloquent\Collection;

class DwfwCollection extends Collection
{
    public function pluckMultiple($value, $key = null)
    {
        preg_match_all('/{(.*?)}/', $key, $matches);
        return $this->flatMap(function ($item) use ($matches, $value, $key) {
            foreach ($matches as $match_index => $match) {
                $key = str_replace($matches[0][$match_index], $item[$matches[1][$match_index]], $key);
            }
            return [$key => $item[$value] ?? $value];
        });
    }
}
