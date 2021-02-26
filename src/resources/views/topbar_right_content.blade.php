<!-- This file is used to store topbar (right) items -->


{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-bell"></i><span class="badge badge-pill badge-danger">5</span></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}
@if (config('dwfw.has_accounts') && (backpack_user()->hasPermissionTo('change account') || count(session('account_ids'))))
<li class="nav-item d-md-down-none mr-4">
    <x-account-selector/>
</li>
@endif
