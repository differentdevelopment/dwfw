<!-- checklist -->
@php
  $model = new \Different\Dwfw\app\Models\Permission;
  $key_attribute = $model->getKeyName();
  $identifiable_attribute = $field['attribute'];

  // calculate the checklist options
  if (!isset($field['options'])) {
      $field['options'] = $model::query()->orderBy('groupname', 'asc')->get()->groupBy('groupname');
  } else {
      $field['options'] = call_user_func($field['options'], $field['model']::query());
  }

  // calculate the value of the hidden input
  $field['value'] = old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? [];
  if ($field['value'] instanceof Illuminate\Database\Eloquent\Collection) {
    $field['value'] = $field['value']->pluck($key_attribute)->toArray();
  } elseif (is_string($field['value'])){
    $field['value'] = json_decode($field['value']);
  }

  // define the init-function on the wrapper
  $field['wrapper']['data-init-function'] =  $field['wrapper']['data-init-function'] ?? 'bpFieldInitChecklist';
@endphp

@include('crud::fields.inc.wrapper_start')
  <input type="hidden" value='@json($field['value'])' name="{{ $field['name'] }}">  
  @foreach ($field['options'] as $label => $items)
      <div class="row mb-2">
        <div class="col-12">
          <label>{{ $label ? trans("backpack::permissionmanager." . $label):trans("backpack::permissionmanager.Standard")}} &ensp; <input id="{{ $label ? $label:'stand' }}" type="checkbox" onchange="toggleAll(this)">&thinsp;{{trans("backpack::permissionmanager.SelectAll")}}</label>
        </div>
          @foreach ($items as $key => $option)          
              <div class="col-sm-4">
                  <div class="checkbox">
                    <label class="font-weight-normal">
                      <input  type="checkbox" name="{{$option->groupname ? $option->groupname:'Standard'}}"  value="{{ $option->id }}"> {{ trans("backpack::permissionmanager.".$option->name) }} 
                    </label>
                    <small>{{trans("backpack::permissionmanager.description")}}</small>
                  </div>
              </div>                      
          @endforeach
      </div>      
  @endforeach
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script>           

          

          function toggleAll(source) {         
            if(source.id == "stand")
            {
              checkboxes = document.getElementsByName("Standard");                       
            }else
            {  
            checkboxes = document.getElementsByName(source.id);           
            }
          checkboxes.forEach(element => element.click());
          }

            function bpFieldInitChecklist(element) {
                var hidden_input = element.find('input[type=hidden]');
                var selected_options = JSON.parse(hidden_input.val() || '[]');
                var checkboxes = element.find('input[type=checkbox]');
                var container = element.find('.row');
                var groupIds = [];
                // set the default checked/unchecked states on checklist options
                checkboxes.each(function(key, option) {
                  var id = $(this).val();                    
                  if (selected_options.map(String).includes(id)) {
                    $(this).prop('checked', 'checked');
                  } else {
                    $(this).prop('checked', false);
                  }
                  if(id == "on")
                  {                   
                    groupIds.push(option.id);                    
                  }
                });    
                  var status = true;
                  for(let i = 0; i < groupIds.length;i++)
                  {             
                    
                    if(groupIds[i] == "stand")
                    {
                      checklist = document.getElementsByName("Standard");
                    }else
                    {
                      checklist = document.getElementsByName(groupIds[i]);
                    }
                    status = true;   
                    checklist.forEach(function(checkelement)
                    {                      
                      if(!selected_options.map(String).includes(checkelement.value))
                      {              
                        status = false;
                      }
                    });                                    
                    if(status)
                    {                      
                      document.getElementById(groupIds[i]).checked=true;
                    }                    
                  }                  

                // when a checkbox is clicked
                // set the correct value on the hidden input
                checkboxes.click(function() {
                  var newValue = [];

                  checkboxes.each(function() {
                    if ($(this).is(':checked')) {
                      if($(this).val()!="on")
                      {
                        var id = $(this).val();                     
                        newValue.push(id);
                      }
                      
                    }
                  });

                  hidden_input.val(JSON.stringify(newValue));

                });
            }           
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}