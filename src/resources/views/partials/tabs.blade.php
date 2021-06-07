<div class="row">
    <div class="{{ isset($crud) ? $crud->getCreateContentClass() : 'col-12' }}">
        <ul class="nav nav-tabs">
            @foreach ($tabs as $tab)
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs($tab['route']) ? ' active' : '' }} {{ isset($tab['only_with_entry']) && $tab['only_with_entry'] && !isset(${$tab['entry_name'] ?? 'entry'}) ? ' disabled ' : '' }}"
                        href="{{ isset(${$tab['entry_name'] ?? 'entry'}) ? route($tab['route'], ${$tab['entry_name'] ?? 'entry'}) : '#'}}"
                    >
                        {{ $tab['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
