<?php

namespace Different\Dwfw\app\Http\Controllers;

use App\Events\MissionUpdated;
use App\Http\Requests\MissionRequest;
use App\Http\Requests\OptionRequest;
use App\Models\Mission;
use App\Models\MissionOption;
use App\Models\Partner;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Prologue\Alerts\Facades\Alert;

abstract class BaseCrudController extends CrudController
{
    //<editor-fold desc="mission handler methods" defaultstate="collapsed">
    /**
     * @param OptionRequest $request
     * @param Partner $partner
     * @param Mission $mission
     * @param $redirect_path
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeMissionOption(OptionRequest $request, Partner $partner, Mission $mission, $redirect_path)
    {
        if (in_array($mission->mission_type, ['KEYBOARD', 'IMAGE', 'VIDEO']) && $mission->mission_options->count()) {
            // admini ellenőrzés, hogy itt maximum 1 opciót lehet feltölteni
            // csak emiatt nem akartam új routot+viewt.. írni a keyboard típusnak a feldolgozására, mint a láncolatnál
            // ha nagyon gáz itt a hardcode, akkor kivezetem úgy (lásd storeOptionChain)
            Alert::error(__('admin.only_one_needed'))->flash();
            return redirect()->back();
        }
        $validated = $request->validated();
        $validated['mission_id'] = $mission->id;

        if ('CHAIN' == $mission->mission_type) {
            $validated['group_code'] = Str::random(30);
            $sequence = round($mission->mission_options()->count() / 2) + 1;
            // az option mindig max számban szerepel, mert null értékkel figyelnek az elemek
            foreach ($validated['option'] as $i => $item) {
                $val['mission_id'] = $validated['mission_id'];
                $val['group_code'] = $validated['group_code'];
                $val['option'] = $validated['option'][$i];
                $val['points'] = !$i ? $validated['points'] : 0;  // csak az első tétel kapja meg a pontértéket
                $val['sequence'] = $sequence;

                if ($request->hasFile('image') && isset($request->image[$i])) {
                    $file = Files::store($request->image[$i], $partner);
                    $val['file_id'] = $file->id;
                } else {
                    $val['file_id'] = null;
                }
                MissionOption::query()->updateOrCreate(['id' => $val['id'] ?? null], $val);
            }

        } else {
            if ($request->hasFile('image')) {
                $file = Files::store($request->image, $partner);
                $validated['file_id'] = $file->id;
            }
            MissionOption::query()->updateOrCreate(['id' => $validated['id'] ?? null], $validated);
        }
        $mission->refresh();

        MissionUpdated::dispatch($mission);
        Alert::success(__('admin.mission_option_saved'))->flash();
        return redirect($redirect_path);
    }

    /**
     * @param Mission $mission
     * @param MissionOption $option
     * @param $redirect_path
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function deleteMissionOption(Mission $mission, MissionOption $option, $redirect_path)
    {
        if (!$option->mission->is($mission)) {
            abort(404);
        }
        // FIXME ha kép volt hozzá feltöltve, akkor itt azt nem kellene törölni?
        if ('CHAIN' == $mission->mission_type) {
            // az összes optiont törölni kell, ami ehhez a feladathoz tartozik és ez a group kódja
            MissionOption::query()->where('mission_id', $mission->id)
                ->where('group_code', $option->group_code)
                ->delete();
        } else {
            $option->delete();
        }
        $mission->refresh();

        MissionUpdated::dispatch($mission);  // mert változni fog a max pontja
        Alert::success(__('admin.deleted'))->flash();
        return redirect($redirect_path);
    }

    /**
     * Store the mission to the selected $foreign_key['key']
     * @param MissionRequest $request
     * @param array $foreign_key
     * @param int $sequence
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function storeMissionReal(MissionRequest $request, array $foreign_key, int $sequence)
    {
        $validated = $request->validated();
        $validated[$foreign_key['key']] = $foreign_key['value'];
        $validated['is_randomized'] = $request->has('is_randomized');
        $validated['address'] = json_decode($validated['address']); // https://github.com/laravel/framework/issues/20115#issuecomment-316120650
        $validated['sequence'] = $validated['sequence'] ?? $sequence;
        $mission = Mission::query()->updateOrCreate(['id' => $validated['id'] ?? null], $validated);
        if (isset($validated['option_sequence'])) {
            MissionOption::updateSequence($mission, $validated['option_sequence'], 'mission_id');
        }
        if ('IMAGE' == $validated['mission_type'] || 'VIDEO' == $validated['mission_type']) {
            $mission_option = $mission->mission_options()->first();
            MissionOption::query()->updateOrCreate(
                ['id' => $mission_option->id ?? null],
                [
                    'mission_id' => $mission->id,
                    'option' => $mission_option->option ?? __('admin.upload_the_' . Str::lower($validated['mission_type'])),
                    'sequence' => 0,
                    'points' => $validated['option_points'],
                ]);
            $mission->refresh();
        }
        MissionUpdated::dispatch($mission);
        Alert::success(__('admin.mission_saved'))->flash();
        return $mission;
    }

    /**
     * @param Mission $mission
     * @param $redirect_url
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function deleteMissionReal(Mission $mission, $redirect_url)
    {
        $mission->delete();
        Alert::success(__('admin.deleted'))->flash();
        return redirect($redirect_url);
    }

    //</editor-fold>

    protected function setupColumnsFieldsFromMethod(): void
    {
        $this->crud->setColumns($this->getColumns());
        $this->crud->addFields($this->getFields());
    }

    abstract protected function getColumns();

    abstract protected function getFields();

    /**
     * Checks for {$column}_id
     * @param string $column The name of the foreign key column, without the "_id" suffix
     */
    protected function checkForColumnId(string $column): void
    {
        if ('partner' == $column) {
            $this->{$column . '_id'} = backpack_user()->hasRole('partner') ? backpack_user()->{$column . '_id'} : Route::current()->parameter($column . '_id');
        } else {
            $this->{$column . '_id'} = Route::current()->parameter($column . '_id');
        }
        if ($this->{$column . '_id'}) {
            $model_name = 'App\Models\\' . ucfirst($column);
            $model = new $model_name;
            $this->{$column} = $model::findOrFail($this->{$column . '_id'});
            $this->crud->setRoute($this->crud->getRoute() . '/' . $this->{$column . '_id'} . '/' . $column);
            $this->crud->setTitle($this->{$column}->name . ' - ' . $this->{$column}->contact_name);
            $this->crud->setHeading($this->{$column}->name . ' - ' . $this->{$column}->contact_name);

            $this->crud->addClause('where', $column . '_id', '=', $this->{$column . '_id'});
            $this->crud->removeField($column . '_id');
            $this->crud->removeColumn($column . '_id');
        }
    }

    /**
     * adds {$column}_id with value to the grid/request before storing data
     * @param string $column The name of the foreign key column, without the "_id" suffix
     * @param null $value
     */
    protected function addColumnIdToRequest(string $column, $value = null): void
    {
        if ($value === null && isset($this->{$column . '_id'})) {
            $value = $this->{$column . '_id'};
        } elseif ($value === '') {
            $value = null;
        } elseif ($value === null) {
            return;
        }
        $this->crud->addField(['name' => $column . '_id', 'type' => 'hidden']);
        $this->crud->getRequest()->request->add([$column . '_id' => $value]);
    }

    /**
     * if {$input_name} file is set, stores the file, adds {$input_name}_id to grid/request
     * @param string $input_name
     * @param int $partner_id
     */
    protected function handleFileUpload(string $input_name, int $partner_id)
    {
        if ($this->crud->getRequest()->hasFile($input_name)) {
            $file = Files::store($this->crud->getRequest()->{$input_name}, $partner_id);
            $this->addColumnIdToRequest($input_name, $file->id);
        } elseif ($this->crud->getRequest()->has($input_name) && $this->crud->getRequest()->$input_name == null) {
            $this->addColumnIdToRequest($input_name, '');
            $this->crud->getRequest()->request->remove($input_name);
        }
    }

    protected function addPartnerFilter()
    {
        $this->crud->addFilter([
            'name' => 'partner_id',
            'type' => 'select2_ajax',
            'label' => __('admin.partner'),
        ],
            route('admin.partner.ajax-partner-list'),
            function ($value) { // if the filter is active
                if ($value) {
                    $this->crud->addClause('where', 'partner_id', $value);
                }
            }
        );
    }

}
