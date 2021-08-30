<!-- dependencyJson -->
@php
  $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
  $field['wrapper']['class'] = $field['wrapper']['class'] ?? 'form-group col-sm-12';
  $field['wrapper']['class'] = $field['wrapper']['class'].' checklist_dependency';
  $field['wrapper']['data-entity'] = $field['wrapper']['data-entity'] ?? $field['field_unique_name'];
  $field['wrapper']['data-init-function'] = $field['wrapper']['init-function'] ?? 'bpFieldInitChecklistDependencyElement';
@endphp

@include('crud::fields.inc.wrapper_start')

    <label>{!! $field['label'] !!}</label>
    <?php
        $entity_model = $crud->getModel();

        //short name for dependency fields
        $primary_dependency = $field['subfields']['primary'];
        $secondary_dependency = $field['subfields']['secondary'];

        //all items with relation
        $dependencies = config('permission.models.role')::with('permissions')->get();

        $dependencyArray = [];

        //convert dependency array to simple matrix ( prymary id as key and array with secondaries id )
        foreach ($dependencies as $primary) {
            $dependencyArray[$primary->id] = [];
            foreach ($primary->{'permissions'} as $secondary) {
                $dependencyArray[$primary->id][] = $secondary->id;
            }
        }

      //for update form, get initial state of the entity
      if (isset($id) && $id) {

        //get entity with relations for primary dependency
          $entity_dependencies = $entity_model->with('roles')
          ->with('roles'.'.'.'permissions')
          ->find($id);

          $secondaries_from_primary = [];

          //convert relation in array
          $primary_array = $entity_dependencies->{'roles'}->toArray();

          $secondary_ids = [];

          //create secondary dependency from primary relation, used to check what chekbox must be check from second checklist
          if (old('roles')) {
              foreach (old('roles') as $primary_item) {
                  foreach ($dependencyArray[$primary_item] as $second_item) {
                      $secondary_ids[$second_item] = $second_item;
                  }
              }
          } else { //create dependecies from relation if not from validate error
              foreach ($primary_array as $primary_item) {
                  foreach ($primary_item[$secondary_dependency['entity']] as $second_item) {
                      $secondary_ids[$second_item['id']] = $second_item['id'];
                  }
              }
          }
      }

        //json encode of dependency matrix
        $dependencyJson = json_encode($dependencyArray);
    ?>

    <div class="container">

      <div class="row">
          <div class="col-sm-12">
              <label>{!! __('backpack::permissionmanager.roles') !!}</label>
              @include('crud::fields.inc.translatable_icon', ['field' => $primary_dependency])
          </div>
      </div>

      <div class="row">

          <div class="hidden_fields_primary" data-name = "{{ 'roles' }}">
          @if(isset($field['value']))
              @if(old('roles'))
                  @foreach( old('roles') as $item )
                  <input type="hidden" class="primary_hidden" name="{{ 'roles' }}[]" value="{{ $item }}">
                  @endforeach
              @else
                  @foreach( $field['value'][0]->pluck('id', 'id')->toArray() as $item )
                  <input type="hidden" class="primary_hidden" name="{{ 'roles' }}[]" value="{{ $item }}">
                  @endforeach
              @endif
            @endif
          </div>

      @foreach (config('permission.models.role')::where(function ($query) {
                    if (!backpack_user()->hasRole('super admin')) {
                        $query->where('name', '<>', 'super admin');
                    }
                } ?? [])->get() as $option)
          <div class="col-sm-{{ isset($primary_dependency['number_columns']) ? intval(12/$primary_dependency['number_columns']) : '4'}}">
              <div class="checkbox">
                  <label class="font-weight-normal">
                      <input type="checkbox"
                          data-id = "{{ $option->id }}"
                          class = 'primary_list'
                          @foreach ($primary_dependency as $attribute => $value)
                              @if (is_string($attribute) && $attribute != 'value')
                                  @if ($attribute=='name')
                                  {{ $attribute }}="{{ $value }}_show[]"
                                  @else
                                  {{ $attribute }}="{{ $value }}"
                                  @endif
                              @endif
                          @endforeach
                          value="{{ $option->id }}"

                          @if( ( isset($field['value']) && is_array($field['value']) && in_array($option->id, $field['value'][0]->pluck('id', 'id')->toArray())) || ( old($primary_dependency["name"]) && in_array($option->id, old( $primary_dependency["name"])) ) )
                          checked = "checked"
                          @endif >
                          {{ $option->{'display_name'} }}
                  </label>
              </div>
          </div>
      @endforeach
      </div>

      <div class="row">
          <div class="col-sm-12">
              <label>{!! ucfirst(__('backpack::permissionmanager.permission_singular')) !!}</label>
              @include('crud::fields.inc.translatable_icon', ['field' => $secondary_dependency])
          </div>
      </div>

      <div class="row">
          <div class="hidden_fields_secondary" data-name="{{ 'permissions' }}">
            @if(isset($field['value']))
              @if(old('permissions'))
                @foreach( old('permissions') as $item )
                  <input type="hidden" class="secondary_hidden" name="{{ 'permissions' }}[]" value="{{ $item }}">                  
                @endforeach
              @else
                @foreach( $field['value'][1]->pluck('id', 'id')->toArray() as $item )
                  <input type="hidden" class="secondary_hidden" name="{{ 'permissions' }}[]" value="{{ $item }}">
                @endforeach
              @endif
            @endif
          </div>
          
          @foreach (config('permission.models.permission')::where($field['secondary_query'] ?? [])->orderBy('groupname', 'asc')->get()->groupBy('groupname') as $label => $items)
          <div class="row mb-2">
            <div class="col-12">
              <label>{{ $label ? trans("backpack::permissionmanager." . $label):trans("backpack::permissionmanager.Standard")}} &ensp; <input id="{{ $label ? $label:'stand' }}" type="checkbox" name="group" onchange="toggleAll(this)">&thinsp;{{trans("backpack::permissionmanager.SelectAll")}}</label>
            </div>
            @foreach ($items as $key => $option)
            <div class="col-sm-4">
              <div class="checkbox">
                      <label class="font-weight-normal">
                      <input type="checkbox"
                          class = 'secondary_list'
                          data-id = "{{ $option->id }}"                         
                          @foreach ($secondary_dependency as $attribute => $value)
                              @if (is_string($attribute) && $attribute != 'value')
                                @if ($attribute=='name')
                                  {{ $attribute }}= "{{ $option -> groupname ? $option->groupname:'Standard' }}_show[]"
                                @else
                                  {{ $attribute }}="{{ $value }}"
                                @endif
                              @endif
                          @endforeach
                          value="{{ $option->id }}"

                          @if( ( isset($field['value']) && is_array($field['value']) && (  in_array($option->id, $field['value'][1]->pluck('id', 'id')->toArray()) || isset( $secondary_ids[$option->id])) || ( old('permissions') &&   in_array($option->id, old('permissions')) )))
                               checked = "checked"
                               @if(isset( $secondary_ids[$option->id]))
                                disabled = disabled
                               @endif
                          @endif > {{ $option->{$secondary_dependency['attribute']} }}
                      </label>                  
              </div>
            </div>
            @endforeach
            </div>
          @endforeach      
    </div><!-- /.container -->


    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}

@push('crud_fields_scripts')
    <script>
        var  {{ $field['field_unique_name'] }} = {!! $dependencyJson !!};
    </script>
@endpush

@if ($crud->checkIfFieldIsFirstOfItsType($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <!-- include checklist_dependency js-->
    <script>
      var groups = new Array;
      function toggleAll(source) {             
            if(source.id == "stand")
            {
              checkboxes = document.getElementsByName("Standard_show[]");                       
            }else
            {  
            checkboxes = document.getElementsByName(source.id+ "_show[]");           
            }
          checkboxes.forEach(element => element.click());
          }
      function bpFieldInitChecklistDependencyElement(element) {          
          var unique_name = element.data('entity');
          var dependencyJson = window[unique_name];
          var thisField = element;          
          thisField.find('.primary_list').change(function(){

            var idCurrent = $(this).data('id');
            if($(this).is(':checked')){

              //add hidden field with this value
              var nameInput = thisField.find('.hidden_fields_primary').data('name');             
              var inputToAdd = $('<input type="hidden" class="primary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

              thisField.find('.hidden_fields_primary').append(inputToAdd);

              $.each(dependencyJson[idCurrent], function(key, value){
                //check and disable secondies checkbox
                thisField.find('input.secondary_list[value="'+value+'"]').prop( "checked", true );                 
                thisField.find('input.secondary_list[value="'+value+'"]').prop( "disabled", true );
                //remove hidden fields with secondary dependency if was setted
                var hidden = thisField.find('input.secondary_hidden[value="'+value+'"]');
                if(hidden)
                  hidden.remove();
              });

            }else{
              //remove hidden field with this value
              thisField.find('input.primary_hidden[value="'+idCurrent+'"]').remove();

              // uncheck and active secondary checkboxs if are not in other selected primary.

              var secondary = dependencyJson[idCurrent];

              var selected = [];
              thisField.find('input.primary_hidden').each(function (index, input){
                selected.push( $(this).val() );
              });

              $.each(secondary, function(index, secondaryItem){
                var ok = 1;

                $.each(selected, function(index2, selectedItem){
                  if( dependencyJson[selectedItem].indexOf(secondaryItem) != -1 ){
                    ok =0;
                  }
                });

                if(ok){
                  thisField.find('input.secondary_list[value="'+secondaryItem+'"]').prop('checked', false);
                  thisField.find('input.secondary_list[value="'+secondaryItem+'"]').prop('disabled', false);
                }
              });

            }
          });


          thisField.find('.secondary_list').click(function(){

            var idCurrent = $(this).data('id');
            if($(this).is(':checked')){
              //add hidden field with this value
              var nameInput = thisField.find('.hidden_fields_secondary').data('name');
              
              var inputToAdd = $('<input type="hidden" class="secondary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');
              console.log(inputToAdd);
              thisField.find('.hidden_fields_secondary').append(inputToAdd);

            }else{
              //remove hidden field with this value
              thisField.find('input.secondary_hidden[value="'+idCurrent+'"]').remove();
            }
          });
              var groupInputs = document.getElementsByName('group');
              var selectedInputs = document.getElementsByClassName('secondary_list');              
              var status = true;
                  for(let i = 0; i < groupInputs.length;i++)
                  {             
                    status = true;
                    console.log(groupInputs[i].id);
                    if(groupInputs[i].id == "stand")
                    {
                      for(let j = 0; j<selectedInputs.length; j++)
                      {                        
                        if(selectedInputs[i].name=="Standard_show[]" && !selectedInputs[i].checked)
                        {
                          console.log("ads")
                            status = false;
                        }
                      }                      
                    }else
                    {
                      for(let j = 0; j<selectedInputs.length; j++)
                      {
                        
                        if(selectedInputs[j].name==groupInputs[i].id+"_show[]" && !selectedInputs[j].checked)
                        {
                          console.log("second for");
                            status = false;
                        }
                      }      
                     
                    }                                           
                    if(status)
                    {           
                      console.log(status);           
                      document.getElementById(groupInputs[i].id).checked=true;
                    }                    
                  } 
      }
    </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
