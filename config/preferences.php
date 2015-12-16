<?php

return [
    'App\Models\Business' => [
        'start_at'                      => ['type' => 'time', 'value' => '08:00:00'],
        'finish_at'                     => ['type' => 'time', 'value' => '19:00:00'],
        'show_map'                      => ['type' => 'bool', 'value' => false],
        'show_postal_address'           => ['type' => 'bool', 'value' => false],
        'show_phone'                    => ['type' => 'bool', 'value' => false],
        'appointment_annulation_pre_hs' => ['type' => 'int', 'value' => '48'],
        'appointment_take_today'        => ['type' => 'bool', 'value' => false],
        'appointment_code_length'       => ['type' => 'int', 'value' => 4],
    ],
];
