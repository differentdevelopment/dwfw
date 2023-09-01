<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@if(auth()->user()->hasAnyPermission('view logs', 'manage users', 'manage settings', 'manage bans') || auth()->user()->hasRole('super admin'))
    {{--    canany blade directive doesnt work--}}
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users-cog"></i> Admin zone</a>
        <ul class="nav-dropdown-items">
            @can('partners.list')
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('partners') }}"><i class="nav-icon la la-user-tie"></i> @lang('dwfw::partners.partners')</a></li>
            @endcan
            @can('manage users')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('users') }}"><i class="nav-icon la la-user"></i> @lang('dwfw::users.users')</a></li>
            @endcan
            @can('manage settings')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('setting') }}"><i class="nav-icon la la-cog"></i> @lang('dwfw::settings.settings')</a></li>
            @endcan
            @can('view logs')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('logs') }}"><i class="nav-icon la la-history"></i> @lang('dwfw::logs.logs')</a></li>
            @endcan
            @can('manage bans')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('spammers') }}"><i class="nav-icon la la-ban"></i> @lang('dwfw::spammers.spammers')</a></li>
            @endcan
            @can('manage permissions')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-clipboard-check"></i> @lang('backpack::permissionmanager.permission_plural')</a></li>
            @endcan
            @can('manage roles')
                <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-users-cog"></i> @lang('backpack::permissionmanager.roles')</a></li>
            @endcan
        </ul>
    </li>
@endif
