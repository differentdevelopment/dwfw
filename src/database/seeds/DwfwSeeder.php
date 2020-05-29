<?php

namespace Different\Dwfw\database\seeds;

use App\Models\User;
use Backpack\Settings\app\Models\Setting;
use Different\Dwfw\app\Models\Partner;
use Different\Dwfw\app\Models\TimeZone;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DwfwSeeder extends Seeder
{
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
            'created_at' => '2020-03-25 08:40:07',
            'updated_at' => '2020-03-25 08:40:07',
        ]);

        // BASE ROLES
        $role_admin = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], []);
        $role_partner = Role::query()->firstOrCreate([
            'name' => 'partner',
            'guard_name' => 'web',
        ], []);

        // add admin role to base user
        $user->assignRole($role_admin->name);

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

        // SETTINGS
        Setting::query()->firstOrCreate([
            'key' => 'privacy_policy',
        ], [
            'name' => 'Adatvédelmi nyilatkozat',
            'description' => 'Adatvédelmi nyilatkozat szövege, amit a regisztrációnál kell elfogadni.',
            'value' => '<p>Normally, both your asses would be dead as fucking fried chicken, but you happen to pull this shit while I&#39;m in a transitional period so I don&#39;t wanna kill you, I wanna help you. But I can&#39;t give you this case, it don&#39;t belong to me. Besides, I&#39;ve already been through too much shit this morning over this case to hand it over to your dumb ass.</p>',
            'field' => '{"name":"value","label":"Value","type":"wysiwyg"}',
            'active' => 1,
            'created_at' => '2020-03-25 08:40:07',
            'updated_at' => '2020-03-25 08:40:07',
        ]);

        // PARTNERS
        $partner = Partner::query()->firstOrCreate([
            'name' => 'Different Fejlesztő Kft.',
        ], [
            'contact_name' => 'Vezető Viktor',
            'contact_phone' => '+362013455467',
            'contact_email' => 'php@different.hu',
        ]);

        $user->partner_id = $partner->id;
        $user->save();

    }
}
