<?php

namespace Different\Dwfw\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Different\Dwfw\app\Http\Requests\AccountChangeRequest;
use Different\Dwfw\app\Models\Account;

class AuthController extends Controller
{
    public function changeAccount(AccountChangeRequest $request)
    {
        $selected_id = $request->validated()['account_id'];
        $user = auth()->user();
        if (
            ($user->hasPermissionTo('change account') || $user->hasRole('super admin') || in_array($selected_id, session('account_ids'))) &&
            (-1 == $selected_id || null != Account::query()->find($selected_id))
        ) {
            session(['account_id' => $selected_id]);
        }
        return config('dwfw.redirect_to_root_after_account_change', false) ? redirect('/') : redirect()->back();
    }
}
