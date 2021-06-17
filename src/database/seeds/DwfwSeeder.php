<?php

namespace Different\Dwfw\database\seeds;

use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Models\Account;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\TimeZone;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DwfwSeeder extends Seeder
{
    const PERMISSIONS = [
        'login backend',
        'manage users',
        'manage bans',
        'view logs',
        'manage settings',
        'manage roles',
        'manage permissions',
    ];

    const ACCOUNT_RELATED_PERMISSIONS = [
        'change account'
    ];

    public function run()
    {
        // USERS
        $user = User::query()->firstOrCreate([
            'email' => 'fejlesztes@different.hu',
        ], [
            'name' => 'Fejlesztés',
            'email_verified_at' => '2020-03-25 08:40:07',
            'password' => '$2y$10$YoqGMgPRGEOUPg4iFRRPqeyqYX3lsNYeZ4fZPqi/jrPaSEBsTVXUK',
            'remember_token' => null,
            'phone' => '+36204377776',
            'created_at' => '2020-03-25 08:40:07',
            'updated_at' => '2020-03-25 08:40:07',
        ]);

        // BASE ROLES
        $role_super_admin = Role::query()->firstOrCreate([
            'name' => 'super admin',
            'guard_name' => 'web',
        ],[]);

        $role_admin = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], []);

        foreach(self::PERMISSIONS as $permission)
        {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ], []);
            $role_admin->givePermissionTo($permission);
        }

        if(config('dwfw.has_accounts')){
            foreach(self::ACCOUNT_RELATED_PERMISSIONS as $permission)
            {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ], []);
                $role_admin->givePermissionTo($permission);
            }
        }


        // add admin role to base user
        $user->assignRole($role_super_admin->name);

        // TIMEZONES
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            TimeZone::query()->firstOrCreate([
                'name' => $zone,
                'diff' => date('P', $timestamp),
            ], []);
        }

        // update users with default timezone
        User::query()->update(['timezone_id' => TimeZone::DEFAULT_TIMEZONE_CODE]);


        // PARTNERS
        $partner = Partner::query()->firstOrCreate([
            'name' => 'Different Fejlesztő Kft.',
        ], [
            'contact_name' => 'Vezető Viktor',
            'contact_phone' => '+362013455467',
            'contact_email' => 'php@different.hu',
        ]);

        $account = Account::query()->firstOrCreate([
            'name' => 'Different',
        ]);
        $account->users()->syncWithoutDetaching($user->id);

        $user->partner_id = $partner->id;
        $user->save();

    }
}
