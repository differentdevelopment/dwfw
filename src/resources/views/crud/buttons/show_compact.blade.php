@if ($crud->hasAccess('show'))
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" bp-button="show" class="btn btn-sm btn-link" title="{{ trans('backpack::crud.preview') }}">
		<i class="la la-eye"></i>
	</a>
@endif
