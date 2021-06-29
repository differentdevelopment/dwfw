<?php

namespace Different\Dwfw\app\Extensions;

use Illuminate\Database\Eloquent\Collection;

class DwfwCollection extends Collection
{
    public function pluckMultiple(string $value, ?string $key = null)
    {
        preg_match_all('/{(.*?)}/', $key, $matches);
        return $this->flatMap(function ($item) use ($matches, $value, $key) {
            foreach ($matches as $match_index => $match) {
                $key = str_replace($matches[0][$match_index], $item[$matches[1][$match_index]], $key);
            }
            return [$key => $item[$value] ?? $value];
        });
    }

    public function pluckArray(array $values, string $key)
    {
        $plucked_array = [];
        $this->each(function ($plucked) use (&$plucked_array, $values, $key) {
            foreach($values as $value){
                $plucked_array[$plucked->$key][$value] = $plucked->$value;
            }
        });
        return $plucked_array;
    }
}
