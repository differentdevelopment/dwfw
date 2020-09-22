<?php

use Different\Dwfw\app\Models\Log;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyLogEntityTypeToFqcn extends Migration
{
    protected array $fqcns = [
        //MB2
        'CAMPAIGN' => 'App\Models\Campaign',
        'MISSION' => 'App\Models\Mission',
        'MISSION_BLOCK' => 'App\Models\MissionBlock',
        'MISSION_TEMPLATE' => 'App\Models\MissionTemplate',
        'SETTING' => 'Backpack\Settings\app\Models\Setting',
        //DWFW
        'USER' => 'App\Models\User',
        'PARTNER' => 'Different\Dwfw\app\Models\Partner',
        ];


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach($this->fqcns as $class => $fqcn){
            Log::query()->where('entity_type', $class)->update(['entity_type' => $fqcn]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
