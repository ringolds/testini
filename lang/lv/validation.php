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

    'accepted' => 'Laukam :attribute jābūt apstiprinātam.',
    'accepted_if' => 'Laukam :attribute jābūt apstiprinātam, ja :other ir :value.',
    'active_url' => 'Lauks :attribute nav derīga URL adrese.',
    'after' => 'Laukam :attribute jābūt datumam pēc :date.',
    'after_or_equal' => 'Laukam :attribute jābūt datumam pēc vai vienādam ar :date.',
    'alpha' => 'Lauks :attribute drīkst saturēt tikai burtus.',
    'alpha_dash' => 'Lauks :attribute drīkst saturēt tikai burtus, skaitļus, domuzīmes un apakšsvītras.',
    'alpha_num' => 'Lauks :attribute drīkst saturēt tikai burtus un skaitļus.',
    'any_of' => 'Lauks :attribute ir nederīgs.',
    'array' => 'Laukam :attribute jābūt masīvam.',
    'ascii' => 'Lauks :attribute drīkst saturēt tikai viena baita burtu un ciparu rakstzīmes un simbolus.',
    'before' => 'Laukam :attribute jābūt datumam pirms :date.',
    'before_or_equal' => 'Laukam :attribute jābūt datumam pirms vai vienādam ar :date.',
    'between' => [
        'array' => 'Laukam :attribute jāsatur no :min līdz :max elementiem.',
        'file' => 'Laukam :attribute jābūt no :min līdz :max kilobaitiem.',
        'numeric' => 'Laukam :attribute jābūt starp :min un :max.',
        'string' => 'Laukam :attribute jābūt no :min līdz :max rakstzīmēm.',
    ],
    'boolean' => 'Laukam :attribute jābūt patiesam vai aplamam (true/false).',
    'can' => 'Lauks :attribute satur neatļautu vērtību.',
    'confirmed' => 'Lauka :attribute apstiprinājums nesakrīt.',
    'contains' => 'Laukam :attribute trūkst obligātas vērtības.',
    'current_password' => 'Parole ir nepareiza.',
    'date' => 'Lauks :attribute nav derīgs datums.',
    'date_equals' => 'Laukam :attribute jābūt datumam, kas vienāds ar :date.',
    'date_format' => 'Laukam :attribute jāatbilst formātam :format.',
    'decimal' => 'Laukam :attribute jāsatur :decimal cipari aiz komata.',
    'declined' => 'Laukam :attribute jābūt noraidītam.',
    'declined_if' => 'Laukam :attribute jābūt noraidītam, ja :other ir :value.',
    'different' => 'Laukiem :attribute un :other jābūt atšķirīgiem.',
    'digits' => 'Laukam :attribute jābūt :digits ciparus garam.',
    'digits_between' => 'Laukam :attribute jābūt no :min līdz :max cipariem.',
    'dimensions' => 'Laukam :attribute ir nederīgi attēla izmēri.',
    'distinct' => 'Laukam :attribute ir dublējoša vērtība.',
    'doesnt_contain' => 'Lauks :attribute nedrīkst saturēt nevienu no šīm vērtībām: :values.',
    'doesnt_end_with' => 'Lauks :attribute nedrīkst beigties ar kādu no šīm vērtībām: :values.',
    'doesnt_start_with' => 'Lauks :attribute nedrīkst sākties ar kādu no šīm vērtībām: :values.',
    'email' => 'Laukam :attribute jābūt derīgai e-pasta adresei.',
    'encoding' => 'Laukam :attribute jābūt kodētam :encoding kodējumā.',
    'ends_with' => 'Laukam :attribute jābeidzas ar kādu no šīm vērtībām: :values.',
    'enum' => 'Izvēlētais :attribute ir nederīgs.',
    'exists' => 'Izvēlētais :attribute ir nederīgs.',
    'extensions' => 'Laukam :attribute jābūt failam ar kādu no šiem paplašinājumiem: :values.',
    'file' => 'Laukam :attribute jābūt failam.',
    'filled' => 'Laukam :attribute jābūt aizpildītam.',
    'gt' => [
        'array' => 'Laukam :attribute jāsatur vairāk nekā :value elementi.',
        'file' => 'Laukam :attribute jābūt lielākam nekā :value kilobaiti.',
        'numeric' => 'Laukam :attribute jābūt lielākam nekā :value.',
        'string' => 'Laukam :attribute jābūt garākam nekā :value rakstzīmes.',
    ],
    'gte' => [
        'array' => 'Laukam :attribute jāsatur vismaz :value elementi.',
        'file' => 'Laukam :attribute jābūt lielākam vai vienādam ar :value kilobaitiem.',
        'numeric' => 'Laukam :attribute jābūt lielākam vai vienādam ar :value.',
        'string' => 'Laukam :attribute jābūt garākam vai vienādam ar :value rakstzīmēm.',
    ],
    'hex_color' => 'Laukam :attribute jābūt derīgai heksadecimālajai (HEX) krāsai.',
    'image' => 'Laukam :attribute jābūt attēlam.',
    'in' => 'Izvēlētais :attribute ir nederīgs.',
    'in_array' => 'Laukam :attribute jāeksistē sarakstā :other.',
    'in_array_keys' => 'Laukam :attribute jāsatur vismaz viena no šīm atslēgām: :values.',
    'integer' => 'Laukam :attribute jābūt veselam skaitlim.',
    'ip' => 'Laukam :attribute jābūt derīgai IP adresei.',
    'ipv4' => 'Laukam :attribute jābūt derīgai IPv4 adresei.',
    'ipv6' => 'Laukam :attribute jābūt derīgai IPv6 adresei.',
    'json' => 'Laukam :attribute jābūt derīgai JSON virknei.',
    'list' => 'Laukam :attribute jābūt sarakstam.',
    'lowercase' => 'Laukam :attribute jābūt rakstītam ar mazajiem burtiem.',
    'lt' => [
        'array' => 'Laukam :attribute jāsatur mazāk nekā :value elementi.',
        'file' => 'Laukam :attribute jābūt mazākam nekā :value kilobaiti.',
        'numeric' => 'Laukam :attribute jābūt mazākam nekā :value.',
        'string' => 'Laukam :attribute jābūt īsākam nekā :value rakstzīmes.',
    ],
    'lte' => [
        'array' => 'Laukam :attribute nedrīkst būt vairāk kā :value elementi.',
        'file' => 'Laukam :attribute jābūt mazākam vai vienādam ar :value kilobaitiem.',
        'numeric' => 'Laukam :attribute jābūt mazākam vai vienādam ar :value.',
        'string' => 'Laukam :attribute jābūt īsākam vai vienādam ar :value rakstzīmēm.',
    ],
    'mac_address' => 'Laukam :attribute jābūt derīgai MAC adresei.',
    'max' => [
        'array' => 'Laukam :attribute nedrīkst būt vairāk kā :max elementi.',
        'file' => 'Laukam :attribute nedrīkst pārsniegt :max kilobaitus.',
        'numeric' => 'Laukam :attribute nedrīkst būt lielāks par :max.',
        'string' => 'Laukam :attribute nedrīkst būt garāks par :max rakstzīmēm.',
    ],
    'max_digits' => 'Laukam :attribute nedrīkst būt vairāk kā :max cipari.',
    'mimes' => 'Laukam :attribute jābūt failam ar tipu: :values.',
    'mimetypes' => 'Laukam :attribute jābūt failam ar tipu: :values.',
    'min' => [
        'array' => 'Laukam :attribute jāsatur vismaz :min elementi.',
        'file' => 'Laukam :attribute jābūt vismaz :min kilobaitus lielam.',
        'numeric' => 'Laukam :attribute jābūt vismaz :min.',
        'string' => 'Laukam :attribute jābūt vismaz :min rakstzīmēm.',
    ],
    'min_digits' => 'Laukam :attribute jāsatur vismaz :min cipari.',
    'missing' => 'Laukam :attribute jābūt izlaistam (nedrīkst būt iesniegts).',
    'missing_if' => 'Laukam :attribute jābūt izlaistam, ja :other ir :value.',
    'missing_unless' => 'Laukam :attribute jābūt izlaistam, izņemot ja :other ir :value.',
    'missing_with' => 'Laukam :attribute jābūt izlaistam, ja ir iesniegts saraksts :values.',
    'missing_with_all' => 'Laukam :attribute jābūt izlaistam, ja ir iesniegti saraksti :values.',
    'multiple_of' => 'Laukam :attribute jābūt skaitļa :value daudzkārtnim.',
    'not_in' => 'Izvēlētais :attribute ir nederīgs.',
    'not_regex' => 'Lauka :attribute formāts ir nederīgs.',
    'numeric' => 'Laukam :attribute jābūt skaitlim.',
    'password' => [
        'letters' => 'Laukam :attribute jāsatur vismaz viens burts.',
        'mixed' => 'Laukam :attribute jāsatur vismaz viens lielais un viens mazais burts.',
        'numbers' => 'Laukam :attribute jāsatur vismaz viens skaitlis.',
        'symbols' => 'Laukam :attribute jāsatur vismaz viens simbols.',
        'uncompromised' => 'Ievadītais lauks :attribute ir noplūdis datu bāzēs. Lūdzu, izvēlies citu :attribute.',
    ],
    'present' => 'Laukam :attribute jābūt iesniegtam.',
    'present_if' => 'Laukam :attribute jābūt iesniegtam, ja :other ir :value.',
    'present_unless' => 'Laukam :attribute jābūt iesniegtam, izņemot ja :other ir :value.',
    'present_with' => 'Laukam :attribute jābūt iesniegtam, ja ir klāt :values.',
    'present_with_all' => 'Laukam :attribute jābūt iesniegtam, ja ir klāt visas :values.',
    'prohibited' => 'Lauka :attribute aizpildīšana ir aizliegta.',
    'prohibited_if' => 'Lauka :attribute aizpildīšana ir aizliegta, ja :other ir :value.',
    'prohibited_if_accepted' => 'Lauka :attribute aizpildīšana ir aizliegta, ja :other ir apstiprināts.',
    'prohibited_if_declined' => 'Lauka :attribute aizpildīšana ir aizliegta, ja :other ir noraidīts.',
    'prohibited_unless' => 'Lauka :attribute aizpildīšana ir aizliegta, izņemot ja :other ir starp :values.',
    'prohibits' => 'Lauks :attribute neļauj iesniegt lauku :other.',
    'regex' => 'Lauka :attribute formāts ir nederīgs.',
    'required' => 'Lauks :attribute ir obligāts.',
    'required_array_keys' => 'Laukam :attribute jāsatur ieraksti priekš: :values.',
    'required_if' => 'Lauks :attribute ir obligāts, ja :other ir :value.',
    'required_if_accepted' => 'Lauks :attribute ir obligāts, ja :other ir apstiprināts.',
    'required_if_declined' => 'Lauks :attribute ir obligāts, ja :other ir noraidīts.',
    'required_unless' => 'Lauks :attribute ir obligāts, izņemot ja :other ir starp :values.',
    'required_with' => 'Lauks :attribute ir obligāts, ja ir iesniegts :values.',
    'required_with_all' => 'Lauks :attribute ir obligāts, ja ir iesniegti visi :values.',
    'required_without' => 'Lauks :attribute ir obligāts, ja nav iesniegts :values.',
    'required_without_all' => 'Lauks :attribute ir obligāts, ja nav iesniegts neviens no :values.',
    'same' => 'Laukiem :attribute un :other jāsakrīt.',
    'size' => [
        'array' => 'Laukam :attribute jāsatur :size elementi.',
        'file' => 'Laukam :attribute jābūt :size kilobaitus lielam.',
        'numeric' => 'Laukam :attribute jābūt :size.',
        'string' => 'Laukam :attribute jābūt :size rakstzīmes garam.',
    ],
    'starts_with' => 'Laukam :attribute jāsākas ar kādu no šīm vērtībām: :values.',
    'string' => 'Laukam :attribute jābūt tekstam.',
    'timezone' => 'Laukam :attribute jābūt derīgai laika zonai.',
    'unique' => 'Šāds :attribute jau ir aizņemts.',
    'uploaded' => 'Lauka :attribute augšupielāde neizdevās.',
    'uppercase' => 'Laukam :attribute jābūt rakstītam ar lielajiem burtiem.',
    'url' => 'Laukam :attribute jābūt derīgai URL adresei.',
    'ulid' => 'Laukam :attribute jābūt derīgam ULID.',
    'uuid' => 'Laukam :attribute jābūt derīgam UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
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
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
