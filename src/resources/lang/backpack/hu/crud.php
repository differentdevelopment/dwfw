<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new'         => 'Mentés és új létrehozása',
    'save_action_save_and_edit'        => 'Mentés és szerkesztés',
    'save_action_save_and_back'        => 'Mentés és vissza a listára',
    'save_action_save_and_preview'     => 'Mentés és megtekintés',
    'save_action_changed_notification' => 'Az alapértelmezett mentés utáni művelet megváltozott',

    // Create form
    'add'                 => 'Hozzáadás:',
    'back_to_all'         => 'Vissza ide: ',
    'cancel'              => 'Mégse',
    'add_a_new'           => 'Új hozzáadása ',

    // Edit form
    'edit'                 => 'Szerkesztés:',
    'save'                 => 'Mentés',

    // Translatable models
    'edit_translations' => 'Fordítások',
    'language'          => 'Nyelv',

    // CRUD table view
    'all'                       => 'Összes ',
    'in_the_database'           => 'adatbázisban',
    'list'                      => 'Lista',
    'reset'                     => 'Visszaállítás',
    'actions'                   => 'Műveletek',
    'preview'                   => 'Előnézet',
    'delete'                    => 'Törlés',
    'admin'                     => 'Admin',
    'details_row'               => 'Ez a bővebben szekció, tetszés szerint módosítható.',
    'details_row_loading_error' => 'Hiba a részletek betöltése közben. Kérjük próbálja újra.',
    'clone' => 'Másolás',
    'clone_success' => '<strong>Bejegyzés másolva</strong><br>Új bejegyzés létrehozva megegyező információkkal.',
    'clone_failure' => '<strong>Másolás sikertelen</strong><br>Az új bejegyzés létrehozása nem sikerült. Kérjük próbáld újra.',

    // Confirmation messages and bubbles
    'delete_confirm'                              => 'Biztosan törli ezt az elemet?',
    'delete_confirmation_title'                   => 'Elem Törölve',
    'delete_confirmation_message'                 => 'Az elem sikeresen törölve.',
    'delete_confirmation_not_title'               => 'Sikertelen törlés',
    'delete_confirmation_not_message'             => "Egy hiba lépett fel. Az elem törlése lehetséges, hogy nem sikerült.",
    'delete_confirmation_not_deleted_title'       => 'Sikertelen törlés',
    'delete_confirmation_not_deleted_message'     => 'Semmi nem történt. Az elem megmaradt.',

    // Bulk actions
    'bulk_no_entries_selected_title'   => 'Nincsenek kiválasztott bejegyzések',
    'bulk_no_entries_selected_message' => 'Egy vagy több elem választása szükséges a tömeges művelethez.',

    // Bulk confirmation
    'bulk_delete_are_you_sure'   => ':number bejegyzés törlése. Biztos?',
    'bulk_delete_sucess_title'   => 'Bejegyzések törölve',
    'bulk_delete_sucess_message' => ' elemek törölve',
    'bulk_delete_error_title'    => 'Sikertelen törlés',
    'bulk_delete_error_message'  => 'Egy vagy több elem nem törölhető',

    // Ajax errors
    'ajax_error_title' => 'Hiba',
    'ajax_error_text'  => 'Hiba az oldal betöltése közben. Kérjük töltse újra az oldalt.',

    // DataTables translation
    'emptyTable'     => 'Nincs rendelkezésre álló adat',
    'info'           => 'Megjelenítve _START_ - _END_ a(z) _TOTAL_ találatból',
    'infoEmpty'      => 'Nincsenek bejegyzések',
    'infoFiltered'   => '(Szűrve _MAX_ elemből)',
    'infoPostFix'    => '.',
    'thousands'      => ',',
    'lengthMenu'     => '_MENU_ bejegyzések oldalanként',
    'loadingRecords' => 'Töltés...',
    'processing'     => 'Feldolgozás...',
    'search'         => 'Keresés: ',
    'zeroRecords'    => 'Nem található a keresésnek megfelelő elem',
    'paginate'       => [
        'first'    => 'Első',
        'last'     => 'Utolsó',
        'next'     => 'Következő',
        'previous' => 'Előző',
    ],
    'aria' => [
        'sortAscending'  => ': Rendezés növekvő sorrendben',
        'sortDescending' => ': Rendezés csökkenő sorrendben',
    ],
    'export' => [
        'export'            => 'Exportálás',
        'copy'              => 'Másolás',
        'excel'             => 'Excel',
        'csv'               => 'CSV',
        'pdf'               => 'PDF',
        'print'             => 'Nyomtatás',
        'column_visibility' => 'Oszlop láthatósága',
    ],

    // global crud - errors
    'unauthorized_access' => 'Nem megfelelő jogosultság - nem rendelkezik megfelelő jogosultsággal az oldal megtekintéséhez.',
    'please_fix'          => 'A következő hibák javítása szükséges:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Elem létrehozva.',
    'update_success' => 'Elem módosítva.',

    // CRUD reorder view

    'reorder'                      => 'Reorder',
    'reorder_text'                 => 'Use drag&drop to reorder.',
    'reorder_success_title'        => 'Done',
    'reorder_success_message'      => 'Your order has been saved.',
    'reorder_error_title'          => 'Error',
    'reorder_error_message'        => 'Your order has not been saved.',

    // CRUD yes/no
    'yes' => 'Igen',
    'no'  => 'Nem',

    // CRUD filters navbar view
    'filters'        => 'Szűrők',
    'toggle_filters' => 'Szűrők kapcsolása',
    'remove_filters' => 'Szűrők törlése',

    // Fields
    'browse_uploads'            => 'Feltöltések böngészése',
    'select_all'                => 'Összes kiválasztása',
    'select_files'              => 'Fájlok kiválasztása',
    'select_file'               => 'Fájl kiválasztása',
    'clear'                     => 'Ürítés',
    'page_link'                 => 'Page link',
    'page_link_placeholder'     => 'http://example.com/your-desired-page',
    'internal_link'             => 'Internal link',
    'internal_link_placeholder' => 'Internal slug. Ex: \'admin/page\' (no quotes) for \':url\'',
    'external_link'             => 'External link',
    'choose_file'               => 'Fájl választása',
    'new_item'                  => 'Új elem',
    'select_entry'              => 'Bejegyzés választása',
    'select_entries'            => 'Bejegyzések választása',

    //Table field
    'table_cant_add'    => 'Nem sikerült hozzáadni új :entity',
    'table_max_reached' => 'A maximális érték :max elérve',

    // File manager
    'file_manager' => 'Fájlkezelő',

    // InlineCreateOperation
    'related_entry_created_success' => 'Kapcsolódó bejegyzés létrehozva és kiválasztva.',
    'related_entry_created_error' => 'Nem sikerült létrehozni a kapcsolódó bejegyzést.',
];
