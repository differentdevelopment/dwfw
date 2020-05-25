<?php

namespace Different\Dwfw\app\Models\Traits;

use App\Models\Partner;
use App\Models\UserToken;
use Creativeorange\Gravatar\Facades\Gravatar;
use Different\Dwfw\app\Models\File;
use Different\Dwfw\app\Models\TimeZone;

trait DwfwUser
{
    protected $guard_name = 'web';

    public function getProfileImage()
    {
        if ($this->profile_image_id) {
            return route('file', $this->profile_image);
        } else {
            return Gravatar::fallback('https://placehold.it/160x160/662d8c/b284d1/&text=' . strtoupper(substr($this->email, 0, 1)))->get($this->email);
        }
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function profile_image()
    {
        return $this->belongsTo(File::class)->whereNull('partner_id');
    }

    public function user_tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function timezone()
    {
        return $this->belongsTo(TimeZone::class);
    }

}
