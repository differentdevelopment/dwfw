<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@role('admin')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-users-cog"></i> Admin zone</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('partners') }}"><i class="nav-icon fa fa-user-tie"></i> @lang('dwfw::partners.partners')</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('users') }}"><i class="nav-icon fa fa-user"></i> @lang('dwfw::users.users')</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('setting') }}"><i class="nav-icon fa fa-cog"></i> @lang('dwfw::settings.settings')</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('spammer') }}"><i class="nav-icon fa fa-cog"></i> @lang('dwfw::spammers.spammers')</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('logs') }}"><i class="nav-icon fa fa-history"></i> @lang('dwfw::logs.logs')</a></li>

    </ul>
</li>
@endrole
