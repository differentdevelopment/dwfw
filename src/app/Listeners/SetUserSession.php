<?php

namespace Different\Dwfw\app\Listeners;


use Different\Dwfw\app\Models\Account;
use Illuminate\Auth\Events\Login;

class SetUserSession
{
    /**
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = Auth()->user();
        //Gate::before super admin not working here, if someone knows the solution, pls develop it. - Urudin
        $accounts = ($user->hasPermissionTo('change account') || $user->hasRole('super admin') ? Account::all() : Auth()->user()->accounts)->pluck('name', 'id')->toArray();
        $account_id = array_key_first($accounts);
        // @deprecated kivezetésre kerül a Minden account egyszerre nézése, több gondot okoz, mint haszna van
//        if (count($accounts) > 1) {
//            $accounts = ['-1' => 'Mind'] + $accounts;
//            if (Auth()->user()->hasRole('dispatcher')) session(['account_id' => -1]);
//        }
        session(['accounts' => $accounts]);
        session(['account_ids' => array_keys($accounts)]);
        if (!session('account_id')) session(['account_id' => $account_id]);
    }
}
