<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'       => 'A(z) :attribute el kell legyen fogadva.',
    'active_url'     => 'A(z) :attribute nem valós URL.',
    'after'          => 'A(z) :attribute :date utáni dátum kell legyen.',
    'after_or_equal' => 'A(z) :attribute egy :date -el megegyező vagy későbbi dátum kell legyen.',
    'alpha'          => 'A(z) :attribute csak betűket tartalmazhat.',
    'alpha_dash'     => 'A(z) :attribute betűket, számokat és kötőjeleket tartalmazhat.',
    'alpha_num'      => 'A(z) :attribute csak betűket és számokat tartalmazhat.',
    'array' => 'A(z) :attribute tömb kell legyen.',
    'before'         => 'A(z) :attribute :date előtti dátum kell legyen.',
    'before_or_equal' => 'A(z) :attribute egy :date -el megegyező vagy korábbi dátum kell legyen.',
    'between'        => [
        'numeric' => 'A(z) :attribute :min - :max közötti érték kell legyen.',
        'file'    => 'A(z) :attribute :min - :max kilobyte között kell legyen.',
        'string'  => 'A(z) :attribute :min - :max karakterhossz között kell legyen',
        'array' => 'A(z) :attribute legalább :min és legfeljebb :max elemet tartalmazhat.',
    ],
    'boolean' => 'A(z) :attribute értéke igaz vagy hamis kell legyen.',
    'confirmed'      => 'A(z) :attribute megerősítése nem egyezett meg.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'date_equals' => 'A(z) :attribute egy :date. -al megegyező dátum kell legyen',
    'date_format' => 'A(z) :attribute a következő formátumban kell legyen: :format.',
    'different'      => 'A(z) :attribute és :other különböző kell legyen.',
    'digits' => 'A(z) :attribute :digits számjegy kell legyen.',
    'digits_between' => 'A(z) :attribute minimum :min és maximum :max számjegyből állhat.',
    'dimensions' => 'A(z) :attribute érvénytelen képméretekkel rendelkezik .',
    'distinct' => 'A(z) :attribute mező duplikált értékeket tartalmaz.',
    'email'          => 'A(z) :attribute formátuma nem megfelelő.',
    'ends_with' => 'A(z) :attribute a következők valamelyikével kell végződjön: :values.',
    'exists'         => 'A(z) választott :attribute nem megfelelő.',
    'file' => 'A(z) :attribute fájl kell legyen.',
    'filled' => 'A(z) :attribute mezőnek rendelkeznie kell értékkel.',
    'gt' => [
        'numeric' => 'A(z) :attribute nagyobb kell legyen, mint :value.',
        'file' => 'A(z) :attribute nagyobb kell legyen, mint :value kilobyte.',
        'string' => 'A(z) :attribute hosszabb kell legyen, mint :value karakter.',
        'array' => 'A(z) :attribute több, mint :value elemet kell tartalmazzon.',
    ],
    'gte' => [
        'numeric' => 'A(z) :attribute nagyobb vagy egyenlő kell legyen, mint :value.',
        'file' => 'A(z) :attribute nagyobb vagy egyenlő kell legyen, mint :value kilobyte.',
        'string' => 'A(z) :attribute legalább :value karakter hosszú kell legyen.',
        'array' => 'A(z) :attribute legalább :value elemet kell tartalmazzon.',
    ],
    'image'          => 'A(z) :attribute kép kell legyen.',
    'in'             => 'A(z) választott :attribute nem megfelelő.',
    'in_array' => 'A(z) :attribute a következők között kell legyen :other.',
    'integer'        => 'A :attribute szám kell legyen.',
    'ip'             => 'A :attribute valós IP cím kell legyen.',
    'ipv4' => 'A(z) :attribute érvényes IPv4 cím kell legyen.',
    'ipv6' => 'A(z) :attribute érvényes IPv6 cím kell legyen..',
    'json' => 'A(z) :attribute érvényes JSON karaktersor kell legyen.',
    'lt' => [
        'numeric' => 'A(z) :attribute kisebb kell legyen, mint :value.',
        'file' => 'A(z) :attribute kisebb kell legyen, mint :value kilobyte.',
        'string' => 'A(z) :attribute rövidebb kell legyen, mint :value karakter.',
        'array' => 'A(z) :attribute :value-nál kevesebb elemet tartalmazhat.',
    ],
    'lte' => [
        'numeric' => 'A(z) :attribute kisebb, vagy egyenlő kell legyen, mint :value.',
        'file' => 'A(z) :attribute legfeljebb :value kilobyte lehet.',
        'string' => 'A(z) :attribute legfeljebb :value karakter hosszú lehet.',
        'array' => 'A(z) :attribute legfeljebb :value elemet tartalmazhat.',
    ],
    'max'            => [
        'numeric' => 'A(z) :attribute kevesebb kell legyen, mint :max.',
        'file'    => 'A(z) :attribute kevesebb kell legyen :max kilobytenál.',
        'string'  => 'A(z) :attribute kevesebb karakterből kell álljon, mint :max.',
        'array' => 'A(z) :attribute kevesebb elemből kell álljon, mint :max.',
    ],
    'mimes'          => 'A(z) :attribute az alábbi tipusokból való kell legyen :values.',
    'mimetypes' => 'A(z) :attribute az alábbi fájltípusok valamelyike kell legyen: :values.',
    'min'            => [
        'numeric' => 'A(z) :attribute legalább :min kell legyen.',
        'file'    => 'A(z) :attribute legalább :min kilobyte kell legyen.',
        'string'  => 'A(z) :attribute legalább :min karakter hosszú kell legyen.',
        'array' => 'A(z) :attribute legalább :min elemet kell tartalmazzon.',
    ],
    'not_in'         => 'A(z) választott :attribute nem megfelelő.',
    'not_regex' => 'A(z) :attribute formátuma érvénytelen.',
    'numeric'        => 'A :attribute szám kell legyen.',
    'password' => 'A megadott jelszó nem megfelelő.',
    'present' => 'A(z) :attribute egy létező érték kell legyen.',
    'regex' => 'A(z) :attribute formátuma érvénytelen.',
    'required'       => 'A(z) :attribute megadása kötelező.',
    'required_if' => 'A(z) :attribute megadása kötelező, ha :other értéke :value.',
    'required_unless' => 'A(z) :attribute megadása kötelező, ha :other értéke nincs a következők között :values.',
    'required_with' => 'A(z) :attribute megadása kötelező, ha :values létezik.',
    'required_with_all' => 'A(z) :attribute megadása kötelező, ha :values léteznek.',
    'required_without' => 'A(z) :attribute megadása kötelező, ha :values nem létezik.',
    'required_without_all' => 'A(z) :attribute megadása kötelező, ha a következők közül egyik sem létezik: :values.',
    'same'           => 'A :attribute és a :other muszáj hogy megegyezzen.',
    'size'           => [
        'numeric' => 'A(z) :attribute :size kell legyen.',
        'file'    => 'A(z) :attribute :size kilobyteos kell legyen.',
        'string'  => 'A(z) :attribute :size karakteres kell legyen.',
        'array' => 'A(z) :attribute pontosan :size elemet kell tartalmazzon.',
    ],
    'starts_with' => 'A(z) :attribute a következők valamelyikével kell kezdődjön: :values.',
    'string' => 'A(z) :attribute karakterlánc kell legyen.',
    'timezone' => 'A(z) :attribute érvényes időzóna kell legyen.',
    'unique'         => 'A(z) :attribute már foglalt.',
    'uploaded' => 'A(z) :attribute feltöltése sikertelen.',
    'url'            => 'A(z) :attribute formátuma nem megfelelő.',
    'uuid' => 'A(z) :attribute érvényes UUID kell legyen.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as 'E-Mail Address' instead
    | of 'email'. This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

