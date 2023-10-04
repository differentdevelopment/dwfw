<?php

namespace Different\Dwfw\app\Models\Traits;

use App\Models\UserToken;
use Carbon\Carbon;
use Creativeorange\Gravatar\Facades\Gravatar;
use Different\Dwfw\app\Models\Account;
use Different\Dwfw\app\Models\File;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\TimeZone;

trait DwfwUser
{
    protected $guard_name = 'web';

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function verify()
    {
        $this->update(['email_verified_at' => Carbon::now()]);
    }

    public function getProfileImage()
    {
        if ($this->profile_image_id) {
            return $this->profile_image;
        } else {
            return Gravatar::fallback('https://placehold.it/160x160/662d8c/b284d1/&text=' . strtoupper(substr($this->email, 0, 1)))->get($this->email);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function user_tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function timezone()
    {
        return $this->belongsTo(TimeZone::class);
    }

    public function file_profile_image()
    {
        return $this->belongsTo(File::class, 'profile_image_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getProfileImageAttribute()
    {
        if (!$this->file_profile_image) {
            return '';
        }
        //add $profile_image_disk to your Model if you are about to use other disk than files for file_profile_image
        return route('file', [$this->profile_image_disk ? $this->profile_image_disk : 'files', $this->file_profile_image]);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
